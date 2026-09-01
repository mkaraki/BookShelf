<?php
namespace App\Controller;

use App\Entity\Author;
use App\Form\Type\AuthorType;
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

class AuthorController extends AbstractController
{
    #[Route('/author/', name: 'author_index')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        $authors = $entityManager->getRepository(Author::class)->findAll();

        return $this->render('author/index.html.twig', [
            'authors' => $authors,
        ]);
    }

    #[Route('/author/new', name: 'author_new')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        // creates an author object and initializes some data for this example
        $author = new Author();

        $form = $this->createForm(AuthorType::class, $author);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $author = $form->getData();

            $entityManager->persist($author);
            $entityManager->flush();

            return $this->redirectToRoute('author_index');
        }

        return $this->render('author/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/author/{id}/', name:'author_show')]
    public function show(EntityManagerInterface $entityManager, Request $request, Author $author): Response
    {
        return $this->render('author/show.html.twig', [
            'author' => $author,
        ]);
    }

    #[Route('/author/{id}/edit', name: 'author_edit')]
    public function edit(EntityManagerInterface $entityManager, Request $request, Author $author): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $form = $this->createForm(AuthorType::class, $author);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $author = $form->getData();

            $entityManager->persist($author);
            $entityManager->flush();

            return $this->redirectToRoute('author_index');
        }

        return $this->render('author/edit.html.twig', [
            'form' => $form,
            'author_name' => $author->getName(),
            'author_id' => $author->getId(),
        ]);
    }

    #[Route('/author/{id}/delete', name:'author_delete', methods: ['POST'])]
    public function delete(EntityManagerInterface $entityManager, Request $request, Author $author): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        if ($request->isMethod('POST')) {
            $entityManager->remove($author);
            $entityManager->flush();

            return $this->redirectToRoute('author_index');
        }

        // Method not allowed.
        throw $this->createNotFoundException();
    }
}
