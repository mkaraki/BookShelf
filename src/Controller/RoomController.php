<?php
namespace App\Controller;

use App\Entity\Author;
use App\Entity\Room;
use App\Entity\Site;
use App\Form\Type\AuthorType;
use App\Form\Type\RoomType;
use App\Form\Type\SiteType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Field\TextareaFormField;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RoomController extends AbstractController
{
    #[Route('/site/{site_id}/room/new', name: 'room_new')]
    public function new(
        EntityManagerInterface $entityManager, Request $request,
        #[MapEntity(expr: 'repository.find(site_id)')]
        Site $site
    ): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $room = new Room();
        $form = $this->createForm(RoomType::class, $room);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $room = $form->getData();

            $room->setParentSite($site);

            $entityManager->persist($room);
            $entityManager->flush();

            return $this->redirectToRoute('site_show', [
                'id' => $site->getId(),
            ]);
        }

        $room->setParentSite($site);

        return $this->render('site/room/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/site/{site_id}/room/{room_id}/', name:'room_show')]
    public function show(
        EntityManagerInterface $entityManager, Request $request,
        #[MapEntity(expr: 'repository.find(site_id)')]
        Site $site,
        #[MapEntity(expr: 'repository.find(room_id)')]
        Room $room
    ): Response
    {
        return $this->render('site/room/show.html.twig', [
            'site' => $site,
            'room' => $room,
        ]);
    }

    #[Route('/room/{id}/', name:'room_show_simple')]
    public function showSimple(
        EntityManagerInterface $entityManager, Request $request,
        Room $room
    ): Response
    {
        return $this->redirectToRoute('room_show', [
            'site_id' => $room->getParentSite()->getId(),
            'room_id' => $room->getId(),
        ]);
    }

    #[Route('/site/{site_id}/room/{room_id}/edit', name: 'room_edit')]
    public function edit(
        EntityManagerInterface $entityManager, Request $request,
        #[MapEntity(expr: 'repository.find(site_id)')]
        Site $site,
        #[MapEntity(expr: 'repository.find(room_id)')]
        Room $room
    ): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $form = $this->createForm(RoomType::class, $room);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $room = $form->getData();

            $entityManager->persist($room);
            $entityManager->flush();

            return $this->redirectToRoute('site_show', [
                'id' => $site->getId(),
            ]);
        }

        return $this->render('site/room/edit.html.twig', [
            'form' => $form,
            'room' => $room,
        ]);
    }

    #[Route('/site/{site_id}/room/{room_id}/delete', name:'room_delete', methods: ['POST'])]
    public function delete(
        EntityManagerInterface $entityManager, Request $request,
        #[MapEntity(expr: 'repository.find(site_id)')]
        Site $site,
        #[MapEntity(expr: 'repository.find(room_id)')]
        Room $room
    ): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $entityManager->remove($room);
        $entityManager->flush();

        return $this->redirectToRoute('site_show', [
            'id' => $site->getId(),
        ]);
    }
}
