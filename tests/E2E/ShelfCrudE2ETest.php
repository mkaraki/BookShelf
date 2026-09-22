<?php

namespace App\Tests\E2E;

final class ShelfCrudE2ETest extends AbstractPantherTestCase
{
    public function testCreateShelfUnderCase(): void
    {
        $this->loginAsAdmin();

        $this->go('/site/1/room/1/case/1/shelf/new');
        $this->assertPageContains('Create new Shelf');
        $this->client->submitForm('Save', ['shelf[shelfNumber]' => '7']);

        $this->assertOnPath('/site/1/room/1/case/1');
        $this->assertPageContains('No. 7');
        $this->assertPageContains('3 shelf(s) found.');
    }

    public function testShowShelfDisplaysDetailsOwnedBooksAndLocation(): void
    {
        $this->go('/site/1/room/1/case/1/shelf/1/');

        $this->assertPageContains('Shelf: 1');
        $this->assertPageContains('Main Site');
        $this->assertPageContains('Living Room');
        $this->assertPageContains('Main Bookcase');
        $this->assertPageContains('Example Novel');
        // shelf code = 01 + id 1 + checksum 1
        $this->assertPageContains('0111');
        $this->assertPageContains('1 book(s) found.');
        $this->assertPageContains('Add New Book');
    }

    public function testEmptyShelfShowsNoBooks(): void
    {
        $this->go('/site/1/room/1/case/1/shelf/2/');

        $this->assertPageContains('Shelf: 2');
        $this->assertPageContains('0 book(s) found.');
        $this->assertPageNotContains('Example Novel');
    }

    public function testSimpleShelfUrlRedirectsToNestedRoute(): void
    {
        $this->go('/shelf/1/');

        $this->assertOnPath('/site/1/room/1/case/1/shelf/1/');
        $this->assertPageContains('Shelf: 1');
    }

    public function testEditShelfNumberThenRedirectsToCase(): void
    {
        $this->loginAsAdmin();

        $this->go('/site/1/room/1/case/1/shelf/2/edit');
        $this->assertPageContains('Edit Shelf #2');
        $this->client->submitForm('Save', ['shelf[shelfNumber]' => '9']);

        // Editing shelf 2 (under case 1) must land back on the case page, not a 404.
        $this->assertOnPath('/site/1/room/1/case/1');
        $this->assertPageContains('Book Case: Main Bookcase');
        $this->assertPageContains('No. 9');
    }

    public function testDeleteEmptyShelf(): void
    {
        $this->loginAsAdmin();

        $this->go('/site/1/room/1/case/1/shelf/new');
        $this->client->submitForm('Save', ['shelf[shelfNumber]' => '3']);
        $this->assertOnPath('/site/1/room/1/case/1');

        // New shelf is id 3 (shelves so far: 1, 2).
        $this->go('/site/1/room/1/case/1');
        $form = $this->client->getCrawler()->filter('form[action="/site/1/room/1/case/1/shelf/3/delete"]')->form();
        $this->client->submit($form);

        $this->assertOnPath('/site/1/room/1/case/1');
        $this->assertPageContains('2 shelf(s) found.');
        $this->assertPageNotContains('No. 3');
    }

    public function testAddingBookPrefillsShelfFromShelfPage(): void
    {
        $this->loginAsAdmin();

        // The "Add New Book" link on the shelf page passes ?shelf_id=2.
        $this->go('/site/1/room/1/case/1/shelf/2/');
        $this->assertPageContains('Add New Book');
        $this->client->clickLink('Add New Book');

        $this->assertOnPath('/ob/new');
        // The parentShelf select already has option 2 selected.
        $selected = $this->client->getCrawler()->filter('#owned_book_parentShelf option[selected]');
        $this->assertCount(1, $selected);
        $this->assertSame('2', $selected->attr('value'));
    }
}