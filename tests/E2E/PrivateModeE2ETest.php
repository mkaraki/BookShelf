<?php

namespace App\Tests\E2E;

final class PrivateModeE2ETest extends AbstractPantherTestCase
{
    private function markPrivate(string $editPath, string $formPrefix): void
    {
        $this->loginAsAdmin();
        $this->go($editPath);
        $this->client->submitForm('Save', [$formPrefix . '[private]' => '1']);
        $this->logout();
    }

    public function testLoggedInUserSeesEverything(): void
    {
        $this->loginAsAdmin();

        $this->go('/site/1/edit');
        $this->client->submitForm('Save', ['site[private]' => '1']);
        $this->go('/book/1/edit');
        $this->client->submitForm('Save', ['book[private]' => '1']);

        $this->go('/site/1/');
        $this->assertStatusCode(200);
        $this->assertPageContains('Living Room');
        $this->assertPageNotContains('private');
        $this->go('/book/1/');
        $this->assertStatusCode(200);
        $this->assertPageNotContains('private');
        $this->go('/ob/');
        $this->assertPageContains('Example Novel');
        $this->assertPageContains('1 collection(s) found.');
        $this->assertPageNotContains('private');
    }

    public function testPrivateBookIsHiddenFromAnonymous(): void
    {
        $this->markPrivate('/book/1/edit', 'book');

        $this->go('/book/');
        $this->assertStatusCode(200);
        $this->assertPageNotContains('Example Novel');
        $this->assertPageContains('Another Book');
        $this->assertPageContains('1 book(s) found.');
        $this->assertPageContains('Some books are private and hidden from anonymous visitors.');
        $this->assertPageContains('1 private book(s) hidden.');

        $this->go('/book/?q=Example');
        $this->assertPageNotContains('Example Novel');
        $this->assertPageContains('0 book(s) found.');

        $this->go('/book/1/');
        $this->assertStatusCode(404);
    }

    public function testPrivateBookHidesItsOwnedBooks(): void
    {
        $this->markPrivate('/book/1/edit', 'book');

        $this->go('/ob/');
        $this->assertPageNotContains('Example Novel');
        $this->assertPageContains('0 collection(s) found.');
        $this->assertPageContains('Some collections are private and hidden from anonymous visitors.');
        $this->assertPageContains('1 private collection(s) hidden.');

        $this->go('/ob/1/');
        $this->assertStatusCode(404);

        $this->go('/site/1/room/1/case/1/');
        $this->assertPageNotContains('Example Novel');
        $this->assertPageContains('1 private book(s) hidden on this shelf.');
    }

    public function testPrivateSiteHidesItsWholeSubtreeAndBooks(): void
    {
        $this->markPrivate('/site/1/edit', 'site');

        $this->go('/site/');
        $this->assertPageNotContains('Main Site');
        $this->assertPageContains('Branch Site');
        $this->assertPageContains('1 site(s) found.');
        $this->assertPageContains('Some sites are private and hidden from anonymous visitors.');
        $this->assertPageContains('1 private site(s) hidden.');

        $this->go('/site/1/');
        $this->assertStatusCode(404);
        $this->go('/site/1/room/1/');
        $this->assertStatusCode(404);
        $this->go('/site/1/room/1/case/1/');
        $this->assertStatusCode(404);
        $this->go('/site/1/room/1/case/1/shelf/1/');
        $this->assertStatusCode(404);

        $this->go('/ob/');
        $this->assertPageNotContains('Example Novel');
        $this->assertPageContains('0 collection(s) found.');
    }

    public function testPrivateRoomHidesItsSubtreeAndBooks(): void
    {
        $this->markPrivate('/site/1/room/1/edit', 'room');

        $this->go('/site/1/');
        $this->assertStatusCode(200);
        $this->assertPageNotContains('Living Room');
        $this->assertPageContains('0 room(s) found.');
        $this->assertPageContains('Some rooms are private and hidden from anonymous visitors.');
        $this->assertPageContains('1 private room(s) hidden.');

        $this->go('/site/1/room/1/');
        $this->assertStatusCode(404);
        $this->go('/site/1/room/1/case/1/');
        $this->assertStatusCode(404);
        $this->go('/site/1/room/1/case/1/shelf/1/');
        $this->assertStatusCode(404);

        $this->go('/ob/');
        $this->assertPageContains('0 collection(s) found.');
    }

    public function testPrivateBookCaseHidesItsSubtreeAndBooks(): void
    {
        $this->markPrivate('/site/1/room/1/case/1/edit', 'book_case');

        $this->go('/site/1/room/1/');
        $this->assertStatusCode(200);
        $this->assertPageNotContains('Main Bookcase');
        $this->assertPageContains('0 case(s) found.');
        $this->assertPageContains('Some cases are private and hidden from anonymous visitors.');
        $this->assertPageContains('1 private case(s) hidden.');

        $this->go('/site/1/room/1/case/1/');
        $this->assertStatusCode(404);
        $this->go('/site/1/room/1/case/1/shelf/1/');
        $this->assertStatusCode(404);

        $this->go('/ob/');
        $this->assertPageContains('0 collection(s) found.');
    }

    public function testPrivateShelfHidesItselfAndItsBooks(): void
    {
        $this->markPrivate('/site/1/room/1/case/1/shelf/1/edit', 'shelf');

        $this->go('/site/1/room/1/case/1/');
        $this->assertStatusCode(200);
        $this->assertPageNotContains('No. 1');
        $this->assertPageContains('No. 2');
        $this->assertPageNotContains('Example Novel');
        $this->assertPageContains('1 shelf(s) found.');
        $this->assertPageContains('Some shelves are private and hidden from anonymous visitors.');
        $this->assertPageContains('1 private shelf(s) hidden.');

        $this->go('/site/1/room/1/case/1/shelf/1/');
        $this->assertStatusCode(404);

        $this->go('/ob/');
        $this->assertPageNotContains('Example Novel');
        $this->assertPageContains('0 collection(s) found.');
    }

    public function testPrivateOwnedBookIsHiddenFromAnonymous(): void
    {
        $this->markPrivate('/ob/1/edit', 'owned_book');

        $this->go('/ob/');
        $this->assertPageNotContains('Example Novel');
        $this->assertPageContains('0 collection(s) found.');
        $this->assertPageContains('Some collections are private and hidden from anonymous visitors.');
        $this->assertPageContains('1 private collection(s) hidden.');

        $this->go('/ob/1/');
        $this->assertStatusCode(404);

        $this->go('/book/1/');
        $this->assertStatusCode(200);
        // The book itself stays public; only its collection is filtered out of "Available Stores".
        $this->assertPageContains('Book: Example Novel');
        $this->assertPageNotContains('0011');
        $this->assertPageContains('0 book(s) found.');
        $this->assertPageContains('Some collections are private and hidden from anonymous visitors.');
        $this->assertPageContains('1 private book(s) hidden.');

        $this->go('/site/1/room/1/case/1/');
        $this->assertPageNotContains('Example Novel');
        $this->assertPageContains('1 private book(s) hidden on this shelf.');

        $this->loginAsAdmin();
        $this->go('/ob/1/');
        $this->assertStatusCode(200);
        $this->assertPageContains('Example Novel');
    }

    public function testHomePageHidesPrivateSitesAndBooks(): void
    {
        $this->markPrivate('/site/1/edit', 'site');
        $this->markPrivate('/book/1/edit', 'book');

        $this->go('/');
        $this->assertStatusCode(200);
        $this->assertPageNotContains('Main Site');
        $this->assertPageContains('Branch Site');
        $this->assertPageNotContains('Example Novel');
        $this->assertPageContains('1 private site(s) hidden.');
        $this->assertPageContains('1 private book(s) hidden.');
    }

    public function testCodeJumpToPrivateEntityReturns404(): void
    {
        $this->markPrivate('/site/1/edit', 'site');

        $this->go('/jump?code=0111');
        $this->assertStatusCode(404);
    }
}
