<?php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomePageController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(Request $request): Response
    {
        return $this->render('index.html.twig');
    }

    public static function calc_bcd_cd(string $code): string {
        $code = str_split(strval($code));
        $sum = 0;
        foreach ($code as $c) {
            if (!is_numeric($c))
                return false;

            $c = intval($c);
            $sum += $c;
            $sum = $sum % 10;
        }

        return strval($sum % 10);
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
        $bcd = HomePageController::calc_bcd_cd($code_content);
        $bcd_expect = substr($bcd, strlen($bcd) - 2, 1);

        if ($bcd != $bcd_expect) {
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
}
