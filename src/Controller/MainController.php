<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Result;
use App\Entity\User;
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

    /**
     * @Route("/start/{id}", name="quiz_start", methods={"GET"})
     * @param Quiz $quiz
     * @return Response
     */
    public function start(Quiz $quiz): Response
    {
        foreach ($quiz->getUsers() as $value)
            if ($value == $this->getUser()) {
                return $this->redirectToRoute("result_questions");
            }
        $quiz->addUser($this->getUser());
        $result = new Result();
        $result->setUser($this->getUser());
        $result->setQuiz($quiz);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($result);
        $entityManager->flush();

        return $this->render('result/questions.html.twig', [
            'quiz' => $quiz,
        ]);
    }

    /**
     * @Route("/result/quiestions", name="result_questions")
     */
    public function playing(): Response
    {
        return $this->render('result/questions.html.twig');
    }

}
