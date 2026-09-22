<?php

namespace App\Tests\E2E;

final class BookCrudE2ETest extends AbstractPantherTestCase
{
    public function testIndexListsBooksAndCount(): void
    {
        $this->go('/book/');

        $this->assertStatusCode(200);
        $this->assertPageContains('Example Novel');
        $this->assertPageContains('Another Book');
        $this->assertPageContains('2 book(s) found.');
    }

    public function testIndexSearchByNameMatchesSubstring(): void
    {
        $this->go('/book/?q=Example');

        $this->assertPageContains('Example Novel');
        $this->assertPageNotContains('Another Book');
        $this->assertPageContains('1 book(s) found.');
    }

    public function testIndexSearchByNameWithNoMatches(): void
    {
        $this->go('/book/?q=zzzz');

        $this->assertPageContains('0 book(s) found.');
        $this->assertPageNotContains('Example Novel');
    }

    public function testIndexSearchByIsbn(): void
    {
        $this->go('/book/?isbn=9784167105860');

        $this->assertPageContains('Example Novel');
        $this->assertPageNotContains('Another Book');
    }

    public function testIndexSearchByIsbnWithNoMatches(): void
    {
        $this->go('/book/?isbn=9781111111111');

        $this->assertPageContains('0 book(s) found.');
    }

    public function testIndexSearchByInvalidIsbnReturnsBadRequest(): void
    {
        $this->go('/book/?isbn=123');

        $this->assertStatusCode(400);
    }

    public function testGenericBookListWhenNoQueryParam(): void
    {
        $this->go('/book/?foo=bar');

        $this->assertPageContains('Example Novel');
        $this->assertPageContains('Another Book');
    }

    public function testAnonymousCannotCreateOrEditBook(): void
    {
        $this->go('/book/new');
        $this->assertOnPath('/login');

        $this->go('/book/1/edit');
        $this->assertOnPath('/login');
    }

    public function testShowBookDisplaysDetailsAndOwnedCopies(): void
    {
        $this->go('/book/1/');

        $this->assertPageContains('Book: Example Novel');
        $this->assertPageContains('9784167105860');
        $this->assertPageContains('Add to Library');
        // owned copy: code + shelf location
        $this->assertPageContains('0011');
        $this->assertPageContainsWs('Main Site / Living Room / Main Bookcase / 1');
        $this->assertPageContains('1 book(s) found.');
    }

    public function testCreateBookWithAllFields(): void
    {
        $this->loginAsAdmin();

        $this->go('/book/new');
        $this->client->submitForm('Save', [
            'book[name]' => 'The Travelling Cat Chronicles',
            'book[bookRead]' => 'トラベル',
            'book[isbn]' => '9784167105860',
            'book[disambiguation]' => 'First edition',
            'book[authors]' => ['1'],
            'book[publisher]' => '1',
        ]);

        $this->assertOnPath('/book/');
        $this->assertPageContains('The Travelling Cat Chronicles');

        $this->go('/book/');
        $this->client->clickLink('The Travelling Cat Chronicles');
        $this->assertPageContains('First edition');
        $this->assertPageContains('トラベル');
    }

    public function testCreateBookWithoutOptionalFields(): void
    {
        $this->loginAsAdmin();

        $this->go('/book/new');
        $this->client->submitForm('Save', ['book[name]' => 'Minimal Book']);

        $this->assertOnPath('/book/');
        $this->assertPageContains('Minimal Book');
        $this->assertPageContains('3 book(s) found.');
    }

    public function testCreateBookWithInvalidIsbnShowsValidationError(): void
    {
        $this->loginAsAdmin();

        $this->go('/book/new');
        $this->client->submitForm('Save', [
            'book[name]' => 'Bad ISBN Book',
            'book[isbn]' => '123',
        ]);

        $this->assertOnPath('/book/new');
        $this->assertPageContains('This value is not valid ISBN-13 code.');
    }

    public function testEditBookChangesNameAndIsbn(): void
    {
        $this->loginAsAdmin();

        $this->go('/book/1/edit');
        $this->assertPageContains('Edit Book #1');
        $this->client->submitForm('Save', [
            'book[name]' => 'Renamed Book',
            'book[isbn]' => '9784167105860',
        ]);

        $this->assertOnPath('/book/');
        $this->assertPageContains('Renamed Book');
        $this->assertPageNotContains('Example Novel');
    }

    public function testDeleteBookWithoutOwnedCopies(): void
    {
        $this->loginAsAdmin();

        $this->go('/book/');
        $form = $this->client->getCrawler()->filter('form[action="/book/2/delete"]')->form();
        $this->client->submit($form);

        $this->assertOnPath('/book/');
        $this->assertPageContains('1 book(s) found.');
        $this->assertPageNotContains('Another Book');
    }

    public function testDeletingBookWithOwnedCopiesFailsConstraint(): void
    {
        $this->loginAsAdmin();

        $this->go('/book/');
        $form = $this->client->getCrawler()->filter('form[action="/book/1/delete"]')->form();
        $this->client->submit($form);

        // owned_book.book_id has no ON DELETE CASCADE, so the copy blocks deletion.
        $this->go('/book/1/');
        $this->assertPageContains('Book: Example Novel');
    }
}