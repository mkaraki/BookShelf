<?php
namespace App\Controller;

use App\Entity\Book;
use App\Entity\Site;
use App\Utils\InternalCodeUtil;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class HomePageController extends AbstractController
{
    public function __construct(
        #[Autowire('%app.version%')]
        private readonly string $appVersion,
    ) {
    }

    #[Route('/', name: 'home')]
    public function index(EntityManagerInterface $entityManager, Request $request): Response
    {
        $sites = $entityManager->getRepository(Site::class)->findAll();
        $recentBooks = $entityManager->getRepository(Book::class)->findRecentlyAdded();

        return $this->render('index.html.twig', [
            'sites' => $sites,
            'recentBooks' => $recentBooks,
        ]);
    }

    #[Route('/jump', name: 'home_code_jump', methods: 'GET')]
    public function codeJump(Request $request): Response
    {
        if (!$request->query->has('code')) {
            return $this->redirectToRoute('home');
        }

        $code = $request->query->get('code');

        if (strlen($code) < 4) {
            // ToDo: Write error.
            return $this->redirectToRoute('home');
        }

        if (strlen($code) == 13 && (str_starts_with($code, '978') || str_starts_with($code, '979'))) {
            return $this->redirectToRoute('book_index', [
                'isbn' => $code,
            ]);
        }

        $code_type = substr($code, 0, 2);
        $code_content = substr($code, 2, strlen($code) - 3);
        $checksum = substr($code, -1);

        if (strval(InternalCodeUtil::calculateBcdCd($code_content)) !== $checksum) {
            throw $this->createNotFoundException(sprintf('Broken code: %s', $code));
        }

        switch ($code_type) {
            case '00':
                // OwnedBook. ToDo.
                return $this->redirectToRoute('ob_show', [
                    'id' => $code_content,
                ]);

            case '01':
                return $this->redirectToRoute('shelf_show_simple', [
                    'id' => $code_content,
                ]);

            case '02':
                return $this->redirectToRoute('book_case_show_simple', [
                    'id' => $code_content,
                ]);

            case '03':
                return $this->redirectToRoute('room_show_simple', [
                    'id' => $code_content,
                ]);

            case '04':
                // User. ToDo.
                throw $this->createNotFoundException(sprintf('User code is not supported yet: %s', $code));

            default:
                throw $this->createNotFoundException(sprintf('Unknown code: %s', $code));
        }
    }

    #[Route(path: '/sysinfo', name: 'home_sysinfo')]
    public function sysinfo(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED');

        return $this->render('sysinfo.html.twig', [
            'appVersion' => $this->appVersion,
        ]);
    }

}
