<?php
declare(strict_types=1);
namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Result;
use App\Form\AnswersQuestionType;
use App\Repository\AnswerRepository;
use App\Repository\QuestionRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlayingController extends AbstractController
{
    /**
     * @Route("/start/{id}", name="start_playing", methods={"GET"})
     * @param Quiz $quiz
     * @param $request
     * @return Response
     */
    public function start(Quiz $quiz,Request $request): Response
    {
        foreach ($quiz->getUsers() as $value)
            if ($value == $this->getUser()) {
                return $this->redirectToRoute("playing_quiz_questions", ['id' => $quiz->getId()]);
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
        return $this->redirectToRoute('playing_quiz_questions', ['id' => $quiz->getId()]);
    }

    /**
     * @Route("/{id}/playing/quiestions", name="playing_quiz_questions")
     * @param Quiz $quiz
     * @param QuestionRepository $questionRepository
     * @param AnswerRepository $answerRepository
     * @param $request
     * @return Response
     */
    public function playing(Quiz $quiz, QuestionRepository $questionRepository, AnswerRepository $answerRepository,Request $request): Response
    {
        $questions = $quiz->getQuestions()->toArray();
        $form = $this->createForm(AnswersQuestionType::class, $questions);
        $form->handleRequest($request);

        return $this->render('playing/questions.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
