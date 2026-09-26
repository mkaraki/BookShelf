<?php

namespace App\Tests\E2E;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\BookCase;
use App\Entity\OwnedBook;
use App\Entity\Publisher;
use App\Entity\Room;
use App\Entity\Shelf;
use App\Entity\Site;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\Panther\Client as PantherClient;
use Symfony\Component\Panther\PantherTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * E2E base against the real web server + real app_test database.
 *
 * Defaults to Panther's HttpBrowserClient (no GUI browser). The app's core is
 * no-JS by design (AGENTS.md), so this exercises the full HTTP stack, real
 * CSRF-protected forms and sessions — identical to the browser flow minus
 * JavaScript execution.
 *
 * To drive a real Firefox instead, set PANTHER_E2E_DRIVER=firefox. That mode
 * currently cannot run on this machine: the macOS App Sandbox of the installed
 * Firefox build blocks it from spawning plugin-container/GPU subprocesses
 * (`sandbox_extension_issue_file_to_process ... Operation not permitted`),
 * so geckodriver's /session handshake never completes.
 */
abstract class AbstractPantherTestCase extends PantherTestCase
{
    public const ADMIN_EMAIL = 'admin@example.com';
    public const ADMIN_PASSWORD = 'admin123';

    protected PantherClient|HttpBrowser $client;
    protected EntityManagerInterface $em;

    protected function setUp(): void
    {
        if (getenv('PANTHER_E2E_DRIVER') === 'firefox') {
            $this->client = static::createPantherClient(['browser' => static::FIREFOX]);
        } else if (getenv('PANTHER_E2E_DRIVER') === 'chrome') {
            $this->client = static::createPantherClient(['browser' => static::CHROME]);
        } else {
            $this->client = self::createHttpBrowserClient();
            $this->restartWebServerIfDown();
        }

        // The Panther browser (and the shared HttpBrowser) is a static singleton
        // that keeps its cookies across every test in the run, so a login from
        // an earlier test would leak into tests that expect an anonymous session.
        $this->client->getCookieJar()->clear();

        $this->em = self::getContainer()->get(EntityManagerInterface::class);
        $this->seedDatabase();
    }

    /**
     * The web server behind the E2E flows is a single-threaded `php -S`
     * built-in server; it occasionally dies mid-suite. Detect that and restart
     * it instead of failing the rest of the run.
     */
    private function restartWebServerIfDown(): void
    {
        $host = '127.0.0.1';
        $port = (int) ($_SERVER['PANTHER_WEB_SERVER_PORT'] ?? 9080);
        $sock = @fsockopen($host, $port, $errno, $errstr, 0.5);
        if ($sock) {
            fclose($sock);

            return;
        }

        static::stopWebServer();
        $this->client = self::createHttpBrowserClient();
    }

    /**
     * Navigate to a page. In real-browser mode the delete forms use inline
     * onsubmit=confirm(...); no-JS clients (and HttpBrowser) submit straight through.
     */
    protected function go(string $uri): void
    {
        $this->client->request('GET', $uri);
        if ($this->client instanceof PantherClient) {
            $this->client->executeScript('window.confirm = function () { return true; };');
        }
    }

    protected function loginAsAdmin(): void
    {
        $this->go('/login');
        $this->client->submitForm('Sign in', [
            '_username' => self::ADMIN_EMAIL,
            '_password' => self::ADMIN_PASSWORD,
        ]);
    }

    protected function logout(): void
    {
        $this->go('/login');
        $this->client->clickLink('Logout');
    }

    protected function pageSource(): string
    {
        return $this->client->getCrawler()->html();
    }

    protected function assertPageContains(string $text): void
    {
        $this->assertStringContainsString($text, $this->pageSource());
    }

    protected function assertPageNotContains(string $text): void
    {
        $this->assertStringNotContainsString($text, $this->pageSource());
    }

    /**
     * Asserts on the page source with all whitespace collapsed to single
     * spaces, so multi-line template output ("Main Site\n/\nLiving Room") can
     * be checked as a single string.
     */
    protected function assertPageContainsWs(string $text): void
    {
        $this->assertStringContainsString($text, preg_replace('/\s+/', ' ', $this->pageSource()));
    }

    protected function assertOnPath(string $path): void
    {
        if ($this->client instanceof PantherClient) {
            $current = $this->client->getCurrentURL();
        } else {
            $current = (string) $this->client->getInternalRequest()->getUri();
        }
        $this->assertStringContainsString($path, $current);
    }

    protected function assertStatusCode(int $code): void
    {
        $this->assertSame($code, $this->client->getInternalResponse()->getStatusCode());
    }

    protected function assertJsonBodyContains(string $text): void
    {
        $content = $this->client->getInternalResponse()->getContent();
        $this->assertStringContainsString($text, $content);
    }

    private function seedDatabase(): void
    {
        $conn = $this->em->getConnection();
        $conn->executeQuery('SET FOREIGN_KEY_CHECKS = 0');
        foreach (['owned_book', 'book_author', 'book', 'author', 'publisher',
                  'book_case', 'shelf', 'room', 'site', 'user', 'messenger_messages'] as $table) {
            $conn->executeStatement(sprintf('TRUNCATE TABLE %s', $table));
        }
        $conn->executeQuery('SET FOREIGN_KEY_CHECKS = 1');

        $hasher = self::getContainer()->get(UserPasswordHasherInterface::class);

        $user = new User();
        $user->setEmail(self::ADMIN_EMAIL);
        $user->setPassword($hasher->hashPassword($user, self::ADMIN_PASSWORD));
        $user->setRoles(['ROLE_ADMIN']);
        $this->em->persist($user);

        $site1 = new Site();
        $site1->setName('Main Site');
        $this->em->persist($site1);

        $site2 = new Site();
        $site2->setName('Branch Site');
        $this->em->persist($site2);

        $room = new Room();
        $room->setName('Living Room');
        $room->setRoomFloor(2);
        $room->setParentSite($site1);
        $this->em->persist($room);

        $case = new BookCase();
        $case->setName('Main Bookcase');
        $case->setParentRoom($room);
        $this->em->persist($case);

        $shelf1 = new Shelf();
        $shelf1->setShelfNumber(1);
        $shelf1->setParentBookCase($case);
        $this->em->persist($shelf1);

        $shelf2 = new Shelf();
        $shelf2->setShelfNumber(2);
        $shelf2->setParentBookCase($case);
        $this->em->persist($shelf2);

        $publisher = new Publisher();
        $publisher->setName('Example Publishing');
        $this->em->persist($publisher);

        $author = new Author();
        $author->setName('Jane Author');
        $author->setAuthorRead('JANE');
        $this->em->persist($author);

        $book1 = new Book();
        $book1->setName('Example Novel');
        $book1->setIsbn('9784167105860');
        $book1->setPublisher($publisher);
        $book1->addAuthor($author);
        $this->em->persist($book1);

        $book2 = new Book();
        $book2->setName('Another Book');
        $this->em->persist($book2);

        $this->em->flush();

        $ob = new OwnedBook();
        $ob->setParentShelf($shelf1);
        $ob->setBook($book1);
        $this->em->persist($ob);
        $this->em->flush();
    }
}
