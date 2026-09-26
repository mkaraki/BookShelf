<?php

namespace App\Tests\E2E;

final class OwnedBookCrudE2ETest extends AbstractPantherTestCase
{
    public function testIndexListsCollectionsWithLocation(): void
    {
        $this->go('/ob/');

        $this->assertStatusCode(200);
        $this->assertPageContains('Collections');
        $this->assertPageContains('Example Novel');
        $this->assertPageContainsWs('Main Site / Living Room / Main Bookcase / 1');
        $this->assertPageContains('1 collection(s) found.');
    }

    public function testAnonymousCannotCreateOwnedBook(): void
    {
        $this->go('/ob/new');
        $this->assertOnPath('/login');
    }

    public function testShowCollectionDisplaysDetails(): void
    {
        $this->go('/ob/1/');

        $this->assertPageContains('Example Novel');
        $this->assertPageContainsWs('Main Site / Living Room / Main Bookcase / 1');
        $this->assertPageContains('Back to List');
    }

    public function testCreateCollectionFromScratch(): void
    {
        $this->loginAsAdmin();

        $this->go('/ob/new');
        $this->client->submitForm('Save', [
            'owned_book[book]' => '1',
            'owned_book[parentShelf]' => '2',
        ]);

        // new() redirects to the book show page.
        $this->assertOnPath('/book/1/');
        $this->assertPageContains('Book: Example Novel');

        $this->go('/ob/');
        $this->assertPageContains('2 collection(s) found.');
        $this->assertPageContainsWs('Main Site / Living Room / Main Bookcase / 2');
    }

    public function testCreateCollectionFromBookPagePrefillsBook(): void
    {
        $this->loginAsAdmin();

        $this->go('/book/1/');
        $this->client->clickLink('Add to Library');

        $this->assertOnPath('/ob/new');
        // Book was prefilled but not shelf; pick one to finish.
        $this->client->submitForm('Save', ['owned_book[parentShelf]' => '2']);

        $this->assertOnPath('/book/1/');
        $this->go('/ob/');
        $this->assertPageContains('2 collection(s) found.');
    }

    public function testEditCollectionMovesItToAnotherShelf(): void
    {
        $this->loginAsAdmin();

        $this->go('/ob/1/edit');
        $this->assertPageContains('Edit Collection #1');
        $this->client->submitForm('Save', ['owned_book[parentShelf]' => '2']);

        $this->assertOnPath('/ob/');
        $this->assertPageContainsWs('Main Site / Living Room / Main Bookcase / 2');

        $this->go('/book/1/');
        $this->assertPageContainsWs('Main Site / Living Room / Main Bookcase / 2');
    }

    public function testDeleteCollection(): void
    {
        $this->loginAsAdmin();

        $this->go('/ob/');
        $form = $this->client->getCrawler()->filter('form[action="/ob/1/delete"]')->form();
        $this->client->submit($form);

        $this->assertOnPath('/ob/');
        $this->assertPageContains('0 collection(s) found.');
        $this->assertPageNotContains('Example Novel');
    }
}