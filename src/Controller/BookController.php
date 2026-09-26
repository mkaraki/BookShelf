<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\Type\BookType;
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

class BookController extends AbstractController
{
    #[Route('/book/', name: 'book_index')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        $books = $entityManager->getRepository(Book::class);

        $isGet = $request->isMethod('GET');

        if ($isGet && $request->query->has('q')) {
            $value = $request->query->get('q');
            $books = $books->findByLikeNameField($value);
        } else if ($isGet && $request->query->has('isbn')) {
            $value = $request->query->get('isbn');
            // Validate
            if (!preg_match('/^[0-9]{13}$/', str_replace('-', '', $value))) {
                // Handle invalid ISBN
                return $this->json([], status: 400);
            }
            $books = $books->findByIsbnField($value);
        } else {
            $books = $books->findAll();
        }

        return $this->render('book/index.html.twig', [
            'books' => $books,
        ]);
    }

    #[Route('/book/new', name: 'book_new')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        // creates a book object and initializes some data for this example
        $book = new Book();

        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($book);
            $entityManager->flush();

            return $this->redirectToRoute('book_index');
        }

        return $this->render('book/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/book/{id}/', name: 'book_show')]
    public function show(EntityManagerInterface $entityManager, Request $request, Book $book): Response
    {
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }

    #[Route('/book/{id}/edit', name: 'book_edit')]
    public function edit(EntityManagerInterface $entityManager, Request $request, Book $book): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $form = $this->createForm(BookType::class, $book);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('book_index');
        }

        return $this->render('book/edit.html.twig', [
            'form' => $form,
            'book' => $book,
        ]);
    }

    #[Route('/book/{id}/delete', name: 'book_delete', methods: ['POST'])]
    public function delete(EntityManagerInterface $entityManager, Request $request, Book $book): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $entityManager->remove($book);
        $entityManager->flush();

        return $this->redirectToRoute('book_index');
    }
}
