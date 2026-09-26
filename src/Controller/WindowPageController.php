<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class WindowPageController extends AbstractController
{
    #[Route('/window/codeReader')]
    public function index(Request $request): Response
    {
        return $this->render('window/codeReader.html.twig');
    }
}
