<?php

namespace App\Tests\E2E;

use App\Entity\Room;
use App\Repository\RoomRepository;

final class RoomCrudE2ETest extends AbstractPantherTestCase
{
    public function testAnonymousCannotAccessRoomCreation(): void
    {
        $this->go('/site/1/room/new');
        $this->assertOnPath('/login');
    }

    public function testCreateRoomUnderSiteAndItAppearsOnSitePage(): void
    {
        $this->loginAsAdmin();

        $this->go('/site/1/room/new');
        $this->assertPageContains('Create new Room');
        $this->client->submitForm('Save', ['room[name]' => 'Study Room']);

        $this->assertOnPath('/site/1/');
        $this->assertPageContains('Study Room');
        $this->assertPageContains('2 room(s) found.');
    }

    public function testCreateRoomLinksToMissingSiteYields404(): void
    {
        $this->loginAsAdmin();

        $this->go('/site/99/room/new');
        $this->assertStatusCode(404);
    }

    public function testRoomUnderMismatchedSiteYields404(): void
    {
        $this->loginAsAdmin();

        $this->go('/site/2/room/new');
        $this->client->submitForm('Save', ['room[name]' => 'Annex Room']);

        // Room id 2 lives in site id 2, so the matching chain works...
        $this->go('/site/2/room/2/');
        $this->assertStatusCode(200);
        $this->assertPageContains('Annex Room');

        // ...but it must not be reachable through site id 1.
        $this->go('/site/1/room/2/');
        $this->assertStatusCode(404);
    }

    public function testShowRoomDisplaysDetailsAndCaseList(): void
    {
        $this->go('/site/1/room/1/');

        $this->assertPageContains('Room: Living Room');
        $this->assertPageContains('Main Bookcase');
        $this->assertPageContains('Add New Case');
        $this->assertPageContains('Back to Site');
        // case code = 02 + id 1 + checksum 1; room code = 03 + id 1 + checksum 1
        $this->assertPageContains('0311');
        $this->assertPageContains('0211');
        $this->assertPageContains('1 case(s) found.');
    }

    public function testSimpleRoomUrlRedirectsToNestedRoute(): void
    {
        $this->go('/room/1/');

        $this->assertOnPath('/site/1/room/1/');
        $this->assertPageContains('Room: Living Room');
    }

    public function testEditRoomChangesName(): void
    {
        $this->loginAsAdmin();

        $this->go('/site/1/room/1/edit');
        $this->assertPageContains('Edit Room #1');
        $this->client->submitForm('Save', ['room[name]' => 'Bathroom']);

        $this->assertOnPath('/site/1/');
        $this->assertPageContains('Bathroom');
        $this->assertPageNotContains('Living Room');
    }

    public function testEditRoomKeepsParentSiteWhenMoved(): void
    {
        $this->loginAsAdmin();

        $this->go('/site/1/room/1/edit');
        $this->client->submitForm('Save', ['room[name]' => 'Moved Room']);

        // Still under site 1.
        $this->assertOnPath('/site/1/');
        $repo = self::getContainer()->get(RoomRepository::class);
        $room = $repo->find(1);
        $this->assertNotNull($room);
        $this->assertSame(1, $room->getParentSite()->getId());
    }

    public function testDeleteEmptyRoom(): void
    {
        $this->loginAsAdmin();

        $this->go('/site/2/room/new');
        $this->client->submitForm('Save', ['room[name]' => 'Empty Room']);
        $this->assertOnPath('/site/2/');
        $this->assertPageContains('Empty Room');

        // The new room is the first one for site 2, so it gets id 2? It is a fresh seed,
        // rooms so far: id 1 under site 1. The new room is id 2.
        $this->go('/site/2/');
        $form = $this->client->getCrawler()->filter('form[action="/site/2/room/2/delete"]')->form();
        $this->client->submit($form);

        $this->assertOnPath('/site/2/');
        $this->assertPageContains('0 room(s) found.');
        $this->assertPageNotContains('Empty Room');
    }

    public function testDeletingRoomWithCasesFailsConstraint(): void
    {
        $this->loginAsAdmin();

        $this->go('/site/1/');
        $form = $this->client->getCrawler()->filter('form[action="/site/1/room/1/delete"]')->form();
        $this->client->submit($form);

        // Case rows still reference the room, so Doctrine refuses to delete it (FK 1451).
        $this->assertStatusCode(500);
        $this->assertNotNull(self::getContainer()->get(RoomRepository::class)->find(1));
    }
}