<?php

namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Result;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlayingController extends AbstractController
{
    /**
     * @Route("/start/{id}", name="start_playing", methods={"GET"})
     * @param Quiz $quiz
     * @return Response
     */
    public function start(Quiz $quiz): Response
    {
        foreach ($quiz->getUsers() as $value)
            if ($value == $this->getUser()) {
                return $this->redirectToRoute("playing_questions");
            }
        $quiz->addUser($this->getUser());
        $result = new Result();
        $result->setUser($this->getUser());
        $result->setQuiz($quiz);
        $quiz->setUsersNumber($quiz->getUsersNumber() + 1);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($result);
        $entityManager->persist($quiz);
        $entityManager->flush();

        return $this->render('playing/questions.html.twig', [
            'quiz' => $quiz,
        ]);
    }

    /**
     * @Route("/playing/quiestions", name="playing_questions")
     */
    public function playing(): Response
    {
        return $this->render('playing/questions.html.twig');
    }
}
