<?php

namespace App\Tests\E2E;

final class AuthorCrudE2ETest extends AbstractPantherTestCase
{
    public function testIndexListsAuthorsAndCount(): void
    {
        $this->go('/author/');

        $this->assertStatusCode(200);
        $this->assertPageContains('Jane Author');
        $this->assertPageContains('1 author(s) found.');
    }

    public function testCreateAuthorWithReadAndDisambiguation(): void
    {
        $this->loginAsAdmin();

        $this->go('/author/new');
        $this->client->submitForm('Save', [
            'author[name]' => 'Hiro Arikawa',
            'author[authorRead]' => 'Aрикава Хиро',
            'author[disambiguation]' => 'The Travelling Cat Chronicles',
        ]);

        $this->assertOnPath('/author/');
        $this->assertPageContains('Hiro Arikawa');

        $this->go('/author/');
        $this->client->clickLink('Hiro Arikawa');
        $this->assertPageContains('Author: Hiro Arikawa');
        $this->assertPageContains('The Travelling Cat Chronicles');
    }

    public function testCreateAuthorWithoutReadAndDisambiguation(): void
    {
        $this->loginAsAdmin();

        $this->go('/author/new');
        $this->client->submitForm('Save', ['author[name]' => 'Bare Author']);

        $this->assertOnPath('/author/');
        $this->assertPageContains('Bare Author');
    }

    public function testShowAuthorDisplaysBooksWritten(): void
    {
        $this->go('/author/1/');

        $this->assertPageContains('Author: Jane Author');
        $this->assertPageContains('Example Novel');
        $this->assertPageContains('1');
        $this->assertPageContains('Back to List');
    }

    public function testEditAuthorChangesName(): void
    {
        $this->loginAsAdmin();

        $this->go('/author/1/edit');
        $this->assertPageContains('Edit author #1');
        $this->client->submitForm('Save', ['author[name]' => 'John Doe']);

        $this->assertOnPath('/author/');
        $this->assertPageContains('John Doe');
        $this->assertPageNotContains('Jane Author');
    }

    public function testDeleteAuthor(): void
    {
        $this->loginAsAdmin();

        $this->go('/author/');
        $form = $this->client->getCrawler()->filter('form[action="/author/1/delete"]')->form();
        $this->client->submit($form);

        $this->assertOnPath('/author/');
        $this->assertPageContains('0 author(s) found.');
        $this->assertPageNotContains('Jane Author');
    }
}