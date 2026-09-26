<?php
namespace App\Controller;

use App\Entity\Author;
use App\Entity\Publisher;
use App\Form\Type\AuthorType;
use App\Form\Type\PublisherType;
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

class PublisherController extends AbstractController
{
    #[Route('/publisher/', name: 'publisher_index')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        $publisher = $entityManager->getRepository(Publisher::class)->findAll();

        return $this->render('publisher/index.html.twig', [
            'publishers' => $publisher
        ]);
    }

    #[Route('/publisher/new', name: 'publisher_new')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        // creates a publisher object and initializes some data for this example
        $publisher = new Publisher();

        $form = $this->createForm(PublisherType::class, $publisher);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $publisher = $form->getData();

            $entityManager->persist($publisher);
            $entityManager->flush();

            return $this->redirectToRoute('publisher_index');
        }

        return $this->render('publisher/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/publisher/{id}/', name:'publisher_show')]
    public function show(EntityManagerInterface $entityManager, Request $request, Publisher $publisher): Response
    {
        return $this->render('publisher/show.html.twig', [
            'publisher' => $publisher,
        ]);
    }

    #[Route('/publisher/{id}/edit', name: 'publisher_edit')]
    public function edit(EntityManagerInterface $entityManager, Request $request, Publisher $publisher): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $form = $this->createForm(PublisherType::class, $publisher);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $publisher = $form->getData();

            $entityManager->persist($publisher);
            $entityManager->flush();

            return $this->redirectToRoute('publisher_index');
        }

        return $this->render('publisher/edit.html.twig', [
            'form' => $form,
            'publisher' => $publisher,
        ]);
    }

    #[Route('/publisher/{id}/delete', name:'publisher_delete', methods: ['POST'])]
    public function delete(EntityManagerInterface $entityManager, Request $request, Publisher $publisher): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $entityManager->remove($publisher);
        $entityManager->flush();

        return $this->redirectToRoute('publisher_index');
    }
}
