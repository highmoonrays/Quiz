<?php
declare(strict_types=1);
namespace App\Controller;

use App\Entity\Quiz;
use App\Repository\QuizRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * Class MainController
 * @package App\Controller
 * @IsGranted("ROLE_USER")
 */
class MainController extends AbstractController
{

    /**
     * @Route("/main", name="main_page", methods={"GET"})
     */
    public function main(QuizRepository $quizRepository): Response
    {
        return $this->render('main/main_page.html.twig', [
            'quizzes' => $quizRepository->findBy(['status' => true]),
        ]);
    }
}
