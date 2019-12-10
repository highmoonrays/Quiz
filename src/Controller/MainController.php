<?php

namespace App\Controller;

use App\Repository\QuizRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{

    /**
     * @Route("/main", name="main_page", methods={"GET"})
     */
    public function main(QuizRepository $quizRepository): Response
    {
        return $this->render('main/main_page.html.twig', [
            'quizzes' => $quizRepository->findAll(),
        ]);
    }
}
