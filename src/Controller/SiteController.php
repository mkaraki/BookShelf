<?php
namespace App\Controller;

use App\Entity\Author;
use App\Entity\Site;
use App\Form\Type\AuthorType;
use App\Form\Type\SiteType;
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

class SiteController extends AbstractController
{
    #[Route('/site/', name: 'site_index')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        $sites = $entityManager->getRepository(Site::class)->findAll();

        return $this->render('site/index.html.twig', [
            'sites' => $sites,
        ]);
    }

    #[Route('/site/new', name: 'site_new')]
    public function new(EntityManagerInterface $entityManager, Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        // creates a site object and initializes some data for this example
        $site = new Site();

        $form = $this->createForm(SiteType::class, $site);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $site = $form->getData();

            $entityManager->persist($site);
            $entityManager->flush();

            return $this->redirectToRoute('site_index');
        }

        return $this->render('site/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/site/{id}/', name:'site_show')]
    public function show(EntityManagerInterface $entityManager, Request $request, Site $site): Response
    {
        return $this->render('site/show.html.twig', [
            'site' => $site,
        ]);
    }

    #[Route('/site/{id}/edit', name: 'site_edit')]
    public function edit(EntityManagerInterface $entityManager, Request $request, Site $site): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $form = $this->createForm(SiteType::class, $site);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $site = $form->getData();

            $entityManager->persist($site);
            $entityManager->flush();

            return $this->redirectToRoute('site_index');
        }

        return $this->render('site/edit.html.twig', [
            'form' => $form,
            'site_name' => $site->getName(),
            'site_id' => $site->getId(),
        ]);
    }

    #[Route('/site/{id}/delete', name:'site_delete', methods: ['POST'])]
    public function delete(EntityManagerInterface $entityManager, Request $request, Site $site): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        $entityManager->remove($site);
        $entityManager->flush();

        return $this->redirectToRoute('site_index');
    }
}
