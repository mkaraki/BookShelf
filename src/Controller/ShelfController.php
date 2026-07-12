<?php
namespace App\Controller;

use App\Entity\Author;
use App\Entity\BookCase;
use App\Entity\Room;
use App\Entity\Shelf;
use App\Entity\Site;
use App\Form\Type\AuthorType;
use App\Form\Type\BookCaseType;
use App\Form\Type\RoomType;
use App\Form\Type\ShelfType;
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

class ShelfController extends AbstractController
{
    #[Route('/site/{site_id}/room/{room_id}/case/{case_id}/shelf/new', name: 'shelf_new')]
    public function new(
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

        $shelf = new Shelf();
        $form = $this->createForm(ShelfType::class, $shelf);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $shelf = $form->getData();

            $shelf->setParentBookCase($case);

            $entityManager->persist($shelf);
            $entityManager->flush();

            return $this->redirectToRoute('book_case_show', [
                'site_id' => $site->getId(),
                'room_id' => $room->getId(),
                'case_id' => $case->getId(),
            ]);
        }

        $shelf->setParentBookCase($case);

        return $this->render('site/room/case/shelf/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/site/{site_id}/room/{room_id}/case/{case_id}/shelf/{shelf_id}/', name:'shelf_show')]
    public function show(
        EntityManagerInterface $entityManager, Request $request,
        #[MapEntity(expr: 'repository.find(site_id)')]
        Site $site,
        #[MapEntity(expr: 'repository.find(room_id)')]
        Room $room,
        #[MapEntity(expr: 'repository.find(case_id)')]
        BookCase $case,
        #[MapEntity(expr: 'repository.find(shelf_id)')]
        Shelf $shelf,
    ): Response
    {
        return $this->render('site/room/case/shelf/show.html.twig', [
            'site' => $site,
            'room' => $room,
            'case' => $case,
            'shelf' => $shelf,
        ]);
    }

    #[Route('/shelf/{id}/', name:'shelf_show_simple')]
    public function showSimple(
        EntityManagerInterface $entityManager, Request $request,
        Shelf $shelf,
    ): Response
    {
        return $this->redirectToRoute('shelf_show', [
            'site_id' => $shelf->getParentBookCase()->getParentRoom()->getParentSite()->getId(),
            'room_id' => $shelf->getParentBookCase()->getParentRoom()->getId(),
            'case_id' => $shelf->getParentBookCase()->getId(),
            'shelf_id' => $shelf->getId(),
        ]);
    }

    #[Route('/site/{site_id}/room/{room_id}/case/{case_id}/shelf/{shelf_id}/edit', name: 'shelf_edit')]
    public function edit(
        EntityManagerInterface $entityManager, Request $request,
        #[MapEntity(expr: 'repository.find(site_id)')]
        Site $site,
        #[MapEntity(expr: 'repository.find(room_id)')]
        Room $room,
        #[MapEntity(expr: 'repository.find(case_id)')]
        BookCase $case,
        #[MapEntity(expr: 'repository.find(shelf_id)')]
        Shelf $shelf,
    ): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $form = $this->createForm(ShelfType::class, $shelf);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $case = $form->getData();

            $entityManager->persist($case);
            $entityManager->flush();

            return $this->redirectToRoute('book_case_show', [
                'site_id' => $site->getId(),
                'room_id' => $room->getId(),
                'case_id' => $case->getId(),
            ]);
        }

        return $this->render('site/room/case/shelf/edit.html.twig', [
            'form' => $form,
            'site' => $site,
            'room' => $room,
            'case' => $case,
            'shelf' => $shelf,
        ]);
    }

    #[Route('/site/{site_id}/room/{room_id}/case/{case_id}/shelf/{shelf_id}/delete', name:'shelf_delete', methods: ['POST'])]
    public function delete(
        EntityManagerInterface $entityManager, Request $request,
        #[MapEntity(expr: 'repository.find(site_id)')]
        Site $site,
        #[MapEntity(expr: 'repository.find(room_id)')]
        Room $room,
        #[MapEntity(expr: 'repository.find(case_id)')]
        BookCase $case,
        #[MapEntity(expr: 'repository.find(shelf_id)')]
        Shelf $shelf,
    ): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $entityManager->remove($shelf);
        $entityManager->flush();

        return $this->redirectToRoute('book_case_show', [
            'site_id' => $site->getId(),
            'room_id' => $room->getId(),
            'case_id' => $case->getId(),
        ]);
    }
}
