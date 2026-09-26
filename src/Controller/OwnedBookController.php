<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\OwnedBook;
use App\Entity\Shelf;
use App\Form\Type\OwnedBookType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DomCrawler\Field\TextareaFormField;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OwnedBookController extends AbstractController
{
    #[Route('/ob/', name: 'ob_index')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        $ownedBooks = $entityManager->getRepository(OwnedBook::class)->findAll();

        return $this->render('ob/index.html.twig', [
            'obs' => $ownedBooks,
        ]);
    }

    #[Route('/ob/new', name: 'ob_new')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        // creates a book object and initializes some data for this example
        $owned_book = new OwnedBook();

        if ($request->isMethod('GET')) {
            if ($request->query->has('shelf_id')) {
                $shelf_id = $request->query->get('shelf_id');
                $shelf = $entityManager->getRepository(Shelf::class)->find($shelf_id);
                if ($shelf) {
                    $owned_book->setParentShelf($shelf);
                }
            }

            if ($request->query->has('book_id')) {
                $book_id = $request->query->get('book_id');
                $book = $entityManager->getRepository(Book::class)->find($book_id);
                if ($book) {
                    $owned_book->setBook($book);
                }
            }
        }

        $form = $this->createForm(OwnedBookType::class, $owned_book);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($owned_book);
            $entityManager->flush();

            return $this->redirectToRoute('book_show', [
                'id' => $owned_book->getBook()->getId(),
            ]);
        }

        return $this->render('ob/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/ob/{id}/', name: 'ob_show')]
    public function show(EntityManagerInterface $entityManager, Request $request, OwnedBook $ownedBook): Response
    {
        return $this->render('ob/show.html.twig', [
            'ob' => $ownedBook,
        ]);
    }

    #[Route('/ob/{id}/edit', name: 'ob_edit')]
    public function edit(EntityManagerInterface $entityManager, Request $request, OwnedBook $ownedBook): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $form = $this->createForm(OwnedBookType::class, $ownedBook);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('ob_index');
        }

        return $this->render('ob/edit.html.twig', [
            'form' => $form,
            'ob' => $ownedBook,
        ]);
    }

    #[Route('/ob/{id}/delete', name: 'ob_delete', methods: ['POST'])]
    public function delete(EntityManagerInterface $entityManager, Request $request, OwnedBook $ownedBook): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $entityManager->remove($ownedBook);
        $entityManager->flush();

        return $this->redirectToRoute('ob_index');
    }
}
