<?php

namespace App\Tests\E2E;

use App\Repository\BookCaseRepository;

final class BookCaseCrudE2ETest extends AbstractPantherTestCase
{
    public function testCreateCaseUnderRoomAndItAppearsOnRoomPage(): void
    {
        $this->loginAsAdmin();

        $this->go('/site/1/room/1/case/new');
        $this->assertPageContains('Create new Book Case');
        $this->client->submitForm('Save', ['book_case[name]' => 'Comics Case']);

        $this->assertOnPath('/site/1/room/1/');
        $this->assertPageContains('Comics Case');
        $this->assertPageContains('2 case(s) found.');
    }

    public function testCaseUnderMismatchedRoomYields404(): void
    {
        $this->loginAsAdmin();

        $this->go('/site/2/room/new');
        $this->client->submitForm('Save', ['room[name]' => 'Annex Room']);
        $this->go('/site/2/room/2/case/new');
        $this->client->submitForm('Save', ['book_case[name]' => 'Annex Case']);

        // Case id 2 lives in room id 2.
        $this->go('/site/2/room/2/case/2');
        $this->assertStatusCode(200);
        $this->assertPageContains('Annex Case');

        // Mismatched case/room pair, and mismatched site/room pair, must both 404.
        $this->go('/site/2/room/2/case/1');
        $this->assertStatusCode(404);
        $this->go('/site/2/room/1/case/1');
        $this->assertStatusCode(404);
    }

    public function testShowCaseDisplaysDetailsShelvesAndOwnedBooks(): void
    {
        $this->go('/site/1/room/1/case/1');

        $this->assertPageContains('Book Case: Main Bookcase');
        $this->assertPageContains('Back to Room');
        $this->assertPageContains('Back to Site');
        $this->assertPageContains('Add New Shelf');
        // owned book code = 00 + id 1 + checksum 1
        $this->assertPageContains('0011');
        $this->assertPageContains('Example Novel');
        $this->assertPageContains('2 shelf(s) found.');
    }

    public function testCaseShelvesAreSortedByNumber(): void
    {
        $this->loginAsAdmin();
        $this->go('/site/1/room/1/case/1/shelf/new');
        $this->client->submitForm('Save', ['shelf[shelfNumber]' => '5']);

        // Third shelf has number 5; the page must show No. 1, No. 2, No. 5 in order.
        $this->go('/site/1/room/1/case/1');
        $this->assertPageContains('3 shelf(s) found.');
        $page = $this->pageSource();
        $this->assertTrue(strpos($page, 'No. 1') < strpos($page, 'No. 2'));
        $this->assertTrue(strpos($page, 'No. 2') < strpos($page, 'No. 5'));
    }

    public function testSimpleCaseUrlRedirectsToNestedRoute(): void
    {
        $this->go('/case/1/');

        $this->assertOnPath('/site/1/room/1/case/1');
        $this->assertPageContains('Book Case: Main Bookcase');
    }

    public function testEditCaseChangesName(): void
    {
        $this->loginAsAdmin();

        $this->go('/site/1/room/1/case/1/edit');
        $this->assertPageContains('Edit Book Case #1');
        $this->client->submitForm('Save', ['book_case[name]' => 'Renamed Case']);

        $this->assertOnPath('/site/1/room/1/');
        $this->assertPageContains('Renamed Case');
        $this->assertPageNotContains('Main Bookcase');
    }

    public function testDeleteEmptyCase(): void
    {
        $this->loginAsAdmin();

        $this->go('/site/1/room/1/case/new');
        $this->client->submitForm('Save', ['book_case[name]' => 'Disposable Case']);
        $this->assertOnPath('/site/1/room/1/');

        // Fresh seed has one case (id 1); the new one is id 2.
        $this->go('/site/1/room/1/');
        $form = $this->client->getCrawler()->filter('form[action="/site/1/room/1/case/2/delete"]')->form();
        $this->client->submit($form);

        $this->assertOnPath('/site/1/room/1/');
        $this->assertPageContains('1 case(s) found.');
        $this->assertPageNotContains('Disposable Case');
    }

    public function testDeletingCaseWithShelvesFailsConstraint(): void
    {
        $this->loginAsAdmin();

        $this->go('/site/1/room/1/');
        $form = $this->client->getCrawler()->filter('form[action="/site/1/room/1/case/1/delete"]')->form();
        $this->client->submit($form);

        // Shelf rows still reference the case, so Doctrine refuses to delete it (FK 1451).
        $this->assertStatusCode(500);
        $this->assertNotNull(self::getContainer()->get(BookCaseRepository::class)->find(1));
    }
}