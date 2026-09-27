<?php

namespace App\Tests\Command;

use App\Command\ImportCommand;
use App\Entity\Book;
use App\Entity\OwnedBook;
use App\Entity\Room;
use App\Entity\Shelf;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Tester\ConsoleAssertionsTrait;
use Symfony\Component\Console\Tester\ExecutionResult;

/**
 * Functional test for `app:import-bookshelf-v1`.
 *
 * The command bypasses the ORM and writes the v1 ids with raw SQL, so besides
 * counting rows everything is also read back through the entities — that is
 * what catches a column-name / mapping drift.
 *
 * The fixture is a hand-picked subset of the real export: four books covering
 * every shape the import has to handle (publisher set/null, author present or
 * not, reading set/null, disambiguation set/null), plus exactly the
 * site/room/case/shelf rows they hang off — so it is ~3.5 KB, contains no
 * registered-but-empty location, and invents no data. The site and room names of
 * the real location are the only edits, replaced by generic ones so no
 * living-area details end up in the repo. `bookCollection` stays a JSON object
 * with `"0"`, `"1"`, … keys, like the real export.
 *
 * Two consequences of using only real data: your export has no book on shelf 1,
 * the only room on floor 1, so the fixture has no non-null `roomFloor` to
 * import; and no book has every optional column null at once, so publisher /
 * reading / disambiguation nulls are each covered by a different book rather
 * than all three by one.
 */
class ImportCommandTest extends KernelTestCase
{
    use ConsoleAssertionsTrait;

    private const FIXTURE = __DIR__.'/../Fixtures/export-test.json';

    /** Import order in the command; child rows go first so truncation is safe. */
    private const TABLES = ['book_author', 'owned_book', 'book', 'author',
        'publisher', 'shelf', 'book_case', 'room', 'site'];

    private EntityManagerInterface $em;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = self::getContainer()->get(EntityManagerInterface::class);

        // The command is documented to need a fresh database, so guarantee it:
        // the explicit v1 ids must not collide with leftovers of a previous run.
        $conn = $this->em->getConnection();
        $conn->executeStatement('SET FOREIGN_KEY_CHECKS = 0');
        foreach (self::TABLES as $table) {
            $conn->executeStatement(sprintf('TRUNCATE TABLE %s', $table));
        }
        $conn->executeStatement('SET FOREIGN_KEY_CHECKS = 1');
    }

    public function testImportsTheWholeLibraryGraph(): void
    {
        $result = $this->import(self::FIXTURE);

        $this->assertCommandIsSuccessful($result);
        $this->assertStringContainsString('Library imported successfully!', $result->getDisplay());
        $this->assertSame([
            'book_author' => 1,
            'owned_book' => 4,
            'book' => 4,
            'author' => 1,
            'publisher' => 1,
            'shelf' => 3,
            'book_case' => 2,
            'room' => 1,
            'site' => 1,
        ], $this->rowCounts());
    }

    /**
     * The interesting part: the four-level site/room/case/shelf chain, both
     * owning sides of the book graph and the nullable columns — all read back
     * through the ORM.
     */
    public function testImportedDataIsNavigableThroughTheEntities(): void
    {
        $this->assertCommandIsSuccessful($this->import(self::FIXTURE));

        // Shelf -> case -> room -> site, walking up from a v1 shelf id.
        $shelf = $this->em->find(Shelf::class, 2);
        $this->assertSame('R01-1', $shelf->getParentBookCase()->getName());
        $this->assertSame('Test Room', $shelf->getParentBookCase()->getParentRoom()->getName());
        $this->assertSame('Test Site', $shelf->getParentBookCase()->getParentRoom()->getParentSite()->getName());

        $this->assertNull($this->em->find(Room::class, 2)->getRoomFloor(), 'null roomFloor survives');

        // Book with a publisher, an author and an ISBN.
        $book = $this->em->find(Book::class, 1);
        $this->assertSame('ぼっち・ざ・ろっく!アンソロジーコミック 1', $book->getName());
        $this->assertSame(9784832274143, $book->getIsbn());
        $this->assertNull($book->getBookRead());
        $this->assertNull($book->getDisambiguation());
        $this->assertSame('芳文社', $book->getPublisher()->getName());
        $this->assertSame('概念', $book->getAuthors()->first()->getDisambiguation());

        // Book with neither publisher nor authors: the nulls must not break
        // the import and must stay null.
        $anonymous = $this->em->find(Book::class, 40);
        $this->assertNull($anonymous->getPublisher());
        $this->assertCount(0, $anonymous->getAuthors());

        // Book carrying both a reading and a disambiguation.
        $annotated = $this->em->find(Book::class, 110);
        $this->assertSame('コノ スバラシイ セカイ ニ シュクフク オ 9', $annotated->getBookRead());
        $this->assertSame('オリジナルアニメブルーレイ同梱版', $annotated->getDisambiguation());

        // owned_book is what puts a book on a shelf; both sides must be filled.
        $owned = $this->em->find(OwnedBook::class, 110);
        $this->assertSame(10, $owned->getParentShelf()->getId());
        $this->assertSame(110, $owned->getBook()->getId());
    }

    /**
     * The auto-increment counters must not be left pointing at the imported
     * ids, otherwise the first book created through the UI collides with a v1
     * row.
     */
    public function testImportedIdsDoNotCollideWithNewlyCreatedRows(): void
    {
        $this->assertCommandIsSuccessful($this->import(self::FIXTURE));
        $this->em->clear();

        $fresh = new Book();
        $fresh->setName('Added after import');
        $this->em->persist($fresh);
        $this->em->flush();

        $this->assertGreaterThan(110, $fresh->getId());
    }

    public function testDecliningTheConfirmationImportsNothing(): void
    {
        $result = $this->import(self::FIXTURE, 'no');

        $this->assertCommandFailed($result);
        $this->assertStringNotContainsString('Library imported successfully!', $result->getDisplay());
        $this->assertSame(array_fill_keys(self::TABLES, 0), $this->rowCounts());
    }

    public function testUnreadableFileFails(): void
    {
        $result = $this->import(__DIR__.'/Fixtures/does-not-exist.json');

        $this->assertCommandFailed($result);
        $this->assertStringContainsString('Cannot read file', $result->getDisplay());
    }

    public function testJsonMissingSectionsFails(): void
    {
        $file = tempnam(sys_get_temp_dir(), 'import');
        file_put_contents($file, json_encode(['sites' => [], 'rooms' => []], JSON_THROW_ON_ERROR));

        try {
            $result = $this->import($file);
            $this->assertCommandFailed($result);
            $this->assertStringContainsString('Invalid JSON structure', $result->getDisplay());
            $this->assertSame(array_fill_keys(self::TABLES, 0), $this->rowCounts());
        } finally {
            unlink($file);
        }
    }

    private function import(string $file, string $answer = 'yes'): ExecutionResult
    {
        $application = new Application();
        $application->addCommand(self::getContainer()->get(ImportCommand::class));
        $tester = new CommandTester($application->find('app:import-bookshelf-v1'));

        return $tester->run(['file' => $file], [$answer], interactive: true);
    }

    /**
     * @return array<string, int>
     */
    private function rowCounts(): array
    {
        $conn = $this->em->getConnection();
        $counts = [];
        foreach (self::TABLES as $table) {
            $counts[$table] = (int) $conn->fetchOne(sprintf('SELECT COUNT(*) FROM %s', $table));
        }

        return $counts;
    }
}
