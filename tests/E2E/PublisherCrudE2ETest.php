<?php

namespace App\Tests\E2E;

final class PublisherCrudE2ETest extends AbstractPantherTestCase
{
    public function testIndexListsPublishersAndCount(): void
    {
        $this->go('/publisher/');

        $this->assertStatusCode(200);
        $this->assertPageContains('Example Publishing');
        $this->assertPageContains('1 publisher(s) found.');
    }

    public function testCreatePublisherWithReadAndDisambiguation(): void
    {
        $this->loginAsAdmin();

        $this->go('/publisher/new');
        $this->client->submitForm('Save', [
            'publisher[name]' => 'Shinchosha',
            'publisher[publisherRead]' => 'シンチョウシャ',
            'publisher[disambiguation]' => 'Japanese publisher',
        ]);

        $this->assertOnPath('/publisher/');
        $this->assertPageContains('Shinchosha');

        $this->go('/publisher/');
        $this->client->clickLink('Shinchosha');
        $this->assertPageContains('Publisher: Shinchosha');
        $this->assertPageContains('Japanese publisher');
    }

    public function testShowPublisherDisplaysBooksPublished(): void
    {
        $this->go('/publisher/1/');

        $this->assertPageContains('Publisher: Example Publishing');
        $this->assertPageContains('Example Novel');
    }

    public function testEditPublisherChangesName(): void
    {
        $this->loginAsAdmin();

        $this->go('/publisher/1/edit');
        $this->assertPageContains('Edit Publisher #1');
        $this->client->submitForm('Save', ['publisher[name]' => 'Acme Books']);

        $this->assertOnPath('/publisher/');
        $this->assertPageContains('Acme Books');
        $this->assertPageNotContains('Example Publishing');
    }

    public function testDeletePublisher(): void
    {
        $this->loginAsAdmin();

        // Publisher 1 is referenced by a book, so deleting it would hit an FK
        // constraint. Create a disposable one and delete that instead.
        $this->go('/publisher/new');
        $this->client->submitForm('Save', ['publisher[name]' => 'Disposable Publishing']);
        $this->assertOnPath('/publisher/');
        $this->assertPageContains('Disposable Publishing');

        $form = $this->client->getCrawler()->filter('form[action="/publisher/2/delete"]')->form();
        $this->client->submit($form);

        $this->assertOnPath('/publisher/');
        $this->assertPageContains('1 publisher(s) found.');
        $this->assertPageNotContains('Disposable Publishing');
    }
}