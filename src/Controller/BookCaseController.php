<?php
namespace App\Controller;

use App\Entity\Author;
use App\Entity\BookCase;
use App\Entity\Room;
use App\Entity\Site;
use App\Form\Type\AuthorType;
use App\Form\Type\BookCaseType;
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

class BookCaseController extends AbstractController
{
    #[Route('/site/{site_id}/room/{room_id}/case/new', name: 'book_case_new')]
    public function new(
        EntityManagerInterface $entityManager, Request $request,
        #[MapEntity(expr: 'repository.find(site_id)')]
        Site $site,
        #[MapEntity(expr: 'repository.find(room_id)')]
        Room $room,
    ): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $bookCase = new BookCase();
        $form = $this->createForm(BookCaseType::class, $bookCase);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $bookCase = $form->getData();

            $bookCase->setParentRoom($room);

            $entityManager->persist($bookCase);
            $entityManager->flush();

            return $this->redirectToRoute('room_show', [
                'site_id' => $site->getId(),
                'room_id' => $room->getId(),
            ]);
        }

        $bookCase->setParentRoom($room);

        return $this->render('site/room/case/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/site/{site_id}/room/{room_id}/case/{case_id}', name:'book_case_show')]
    public function show(
        EntityManagerInterface $entityManager, Request $request,
        #[MapEntity(expr: 'repository.find(site_id)')]
        Site $site,
        #[MapEntity(expr: 'repository.find(room_id)')]
        Room $room,
        #[MapEntity(expr: 'repository.find(case_id)')]
        BookCase $case,
    ): Response
    {
        return $this->render('site/room/case/show.html.twig', [
            'site' => $site,
            'room' => $room,
            'case' => $case,
        ]);
    }

    #[Route('/case/{id}/', name:'book_case_show_simple')]
    public function showSimple(
        EntityManagerInterface $entityManager, Request $request,
        BookCase $case,
    ): Response
    {
        return $this->redirectToRoute('book_case_show', [
            'site_id' => $case->getParentRoom()->getParentSite()->getId(),
            'room_id' => $case->getParentRoom()->getId(),
            'case_id' => $case->getId(),
        ]);
    }

    #[Route('/site/{site_id}/room/{room_id}/case/{case_id}/edit', name: 'book_case_edit')]
    public function edit(
        EntityManagerInterface $entityManager, Request $request,
        #[MapEntity(expr: 'repository.find(site_id)')]
        Site $site,
        #[MapEntity(expr: 'repository.find(room_id)')]
        Room $room,
        #[MapEntity(expr: 'repository.find(case_id)')]
        BookCase $case,
    ): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $form = $this->createForm(BookCaseType::class, $case);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $case = $form->getData();

            $entityManager->persist($case);
            $entityManager->flush();

            return $this->redirectToRoute('room_show', [
                'site_id' => $site->getId(),
                'room_id' => $room->getId(),
            ]);
        }

        return $this->render('site/room/case/edit.html.twig', [
            'form' => $form,
            'site' => $site,
            'room' => $room,
            'case' => $case,
        ]);
    }

    #[Route('/site/{site_id}/room/{room_id}/case/{case_id}/delete', name:'book_case_delete', methods: ['POST'])]
    public function delete(
        EntityManagerInterface $entityManager, Request $request,
        #[MapEntity(expr: 'repository.find(site_id)')]
        Site $site,
        #[MapEntity(expr: 'repository.find(room_id)')]
        Room $room,
        #[MapEntity(expr: 'repository.find(case_id)')]
        BookCase $case
    ): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $entityManager->remove($case);
        $entityManager->flush();

        return $this->redirectToRoute('room_show', [
            'site_id' => $site->getId(),
            'room_id' => $room->getId(),
        ]);
    }
}
