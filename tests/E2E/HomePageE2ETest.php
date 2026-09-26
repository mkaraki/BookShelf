<?php

namespace App\Tests\E2E;

final class HomePageE2ETest extends AbstractPantherTestCase
{
    public function testHomeShowsSitesAndSearchForms(): void
    {
        $this->go('/');

        $this->assertStatusCode(200);
        $this->assertPageContains('BookShelf');
        $this->assertPageContains('Main Site');
        $this->assertPageContains('Branch Site');
        $this->assertPageContains('Search Book by Name');
        $this->assertPageContains('Search Book by ISBN');
        $this->assertPageContains('Jump to Code');
        $this->assertPageContains('Recently Added Books');
    }

    public function testRecentlyAddedOnlyListsOwnedBooks(): void
    {
        // book1 'Example Novel' is owned, book2 'Another Book' is not.
        $this->go('/');

        $this->assertPageContains('Example Novel');
        $this->assertPageNotContains('Another Book');
    }

    public function testMainSiteLinkLeadsToSiteShow(): void
    {
        $this->go('/');
        $this->client->clickLink('Main Site');

        $this->assertOnPath('/site/1/');
        $this->assertPageContains('Site: Main Site');
    }

    public function testExampleNovelLinkLeadsToBookShow(): void
    {
        $this->go('/');
        $this->client->clickLink('Example Novel');

        $this->assertOnPath('/book/1/');
        $this->assertPageContains('Book: Example Novel');
    }

    public function testJumpWithMissingCodeRedirectsHome(): void
    {
        $this->go('/jump');

        $this->assertOnPath('/');
    }

    public function testJumpWithTooShortCodeRedirectsHome(): void
    {
        $this->go('/jump?code=12');

        $this->assertOnPath('/');
    }

    public function testJumpWithIsbnRedirectsToBookSearch(): void
    {
        $this->go('/jump?code=9784167105860');

        $this->assertOnPath('/book/');
        $this->assertPageContains('Example Novel');
    }

    public function testJumpWithValidShelfCodeRedirectsToShelf(): void
    {
        // code = 01 (shelf) + id 1 + checksum 1
        $this->go('/jump?code=0111');

        $this->assertOnPath('/site/1/room/1/case/1/shelf/1/');
        $this->assertPageContains('Shelf: 1');
    }

    public function testJumpWithValidBookCaseCodeRedirectsToCase(): void
    {
        // code = 02 (case) + id 1 + checksum 1
        $this->go('/jump?code=0211');

        $this->assertOnPath('/site/1/room/1/case/1');
        $this->assertPageContains('Book Case: Main Bookcase');
    }

    public function testJumpWithValidRoomCodeRedirectsToRoom(): void
    {
        // code = 03 (room) + id 1 + checksum 1
        $this->go('/jump?code=0311');

        $this->assertOnPath('/site/1/room/1/');
        $this->assertPageContains('Room: Living Room');
    }

    public function testJumpWithBrokenChecksumReturns404(): void
    {
        // shelf code for id 1, but last digit should be 1, not 9.
        $this->go('/jump?code=0119');

        $this->assertStatusCode(404);
    }

    public function testJumpWithOwnedBookCodeRedirectsToCollection(): void
    {
        // code = 00 (owned book) + id 1 + checksum 1
        $this->go('/jump?code=0011');

        $this->assertOnPath('/ob/1/');
        $this->assertPageContains('Collection #1');
    }

    public function testJumpWithUserCodeTypeIsNotSupported(): void
    {
        // code = 04 (user) + id 1 + checksum 1
        $this->go('/jump?code=0411');

        $this->assertStatusCode(404);
        $this->assertPageContains('not supported');
    }

    public function testJumpWithUnknownCodeTypeReturns404(): void
    {
        $this->go('/jump?code=9911');

        $this->assertStatusCode(404);
    }
}