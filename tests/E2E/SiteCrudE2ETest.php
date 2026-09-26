<?php

namespace App\Tests\E2E;

use App\Repository\SiteRepository;

final class SiteCrudE2ETest extends AbstractPantherTestCase
{
    public function testIndexListsSitesAndCount(): void
    {
        $this->go('/site/');

        $this->assertStatusCode(200);
        $this->assertPageContains('Main Site');
        $this->assertPageContains('Branch Site');
        $this->assertPageContains('2 site(s) found.');
    }

    public function testAnonymousCannotCreateOrEditSite(): void
    {
        $this->go('/site/new');
        $this->assertOnPath('/login');

        $this->go('/site/1/edit');
        $this->assertOnPath('/login');
    }

    public function testCreateSiteAndShowIt(): void
    {
        $this->loginAsAdmin();

        $this->go('/site/new');
        $this->client->submitForm('Save', ['site[name]' => 'My New Site']);

        $this->assertOnPath('/site/');
        $this->assertPageContains('My New Site');

        $this->go('/site/');
        $this->client->clickLink('My New Site');
        $this->assertOnPath('/');
        $this->assertPageContains('Site: My New Site');
        $this->assertPageContains('0 room(s) found.');
    }

    public function testSiteShowShowsRoomsWithCodesAndLinks(): void
    {
        $this->go('/site/1/');

        $this->assertPageContains('Site: Main Site');
        $this->assertPageContains('Living Room');
        // room code = 03 + id 1 + checksum 1
        $this->assertPageContains('0311');
        $this->assertPageContains('1 room(s) found.');
        $this->assertPageContains('Add New Room');
    }

    public function testEditSiteChangesName(): void
    {
        $this->loginAsAdmin();

        $this->go('/site/1/edit');
        $this->assertPageContains('Edit Site #1');
        $this->client->submitForm('Save', ['site[name]' => 'Renamed Site']);

        $this->assertOnPath('/site/');
        $this->assertPageContains('Renamed Site');
        $this->assertPageNotContains('Main Site');
    }

    public function testDeleteSite(): void
    {
        $this->loginAsAdmin();

        $this->go('/site/2/delete');
        $this->assertStatusCode(405);

        $this->go('/site/');
        $this->assertPageContains('Branch Site');

        $form = $this->client->getCrawler()->filter('form[action="/site/2/delete"]')->form();
        $this->client->submit($form);

        $this->assertOnPath('/site/');
        $this->assertPageContains('1 site(s) found.');
        $this->assertPageNotContains('Branch Site');
    }

    public function testDeletingSiteWithChildrenFailsConstraint(): void
    {
        $this->loginAsAdmin();

        $this->go('/site/');
        $form = $this->client->getCrawler()->filter('form[action="/site/1/delete"]')->form();
        $this->client->submit($form);

        // Room 1 still points at site 1, so Doctrine refuses to delete it (FK 1451).
        $this->assertStatusCode(500);
        $this->assertNotNull(self::getContainer()->get(SiteRepository::class)->find(1));
    }
}