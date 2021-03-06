<?php
declare(strict_types=1);
namespace App\Controller;

use App\Entity\Quiz;
use App\Entity\Result;
use App\Form\AnswersQuestionType;
use App\Form\AnswerType;
use App\Form\ForAnswersType;
use App\Repository\AnswerRepository;
use App\Repository\QuestionRepository;
use App\Repository\ResultRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;


/**
 * Class PlayingController
 * @package App\Controller
 * @IsGranted("ROLE_USER")
 */
class PlayingController extends AbstractController
{
    /**
     * @Route("/start/{id}", name="start_playing", methods={"GET"})
     * @param Quiz $quiz
     * @param $request
     * @return Response
     *
     */
    public function start(Quiz $quiz,Request $request): Response
    {
        foreach ($quiz->getUsers() as $user)
            if ($user == $this->getUser()) {
                $result = $this->getDoctrine()
                    ->getRepository(Result::class)
                    ->findOneBy(array('user' => $user, 'quiz' => $quiz));
                return $this->redirectToRoute('playing_quiz_questions', [
                    'id' => $result->getId(),
                ]);
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
        return $this->redirectToRoute('playing_quiz_questions', [
            'id' => $result->getId(),
        ]);
    }

    /**
     * @param Result $result
     * @param Request $request
     * @return Response
     * @Route("/{id}/playing/quiestions", name="playing_quiz_questions", methods={"GET", "POST"})
     * @Entity("result", expr="repository.find(id)")
     * @return Response
     */
    public function playing( Result $result, Request $request): Response
    {
        $answered_questions = $result->getQuestions()->toArray();
        $number_of_answered_questions = count($answered_questions);
        $questions = $result->getQuiz()->getQuestions()->toArray();
        if ($number_of_answered_questions == 0) {
            $question = $questions[0];
            $answers = $question->getAnswers();
        } // top of players here
        elseif ($number_of_answered_questions == count($questions)) {
            return $this->redirectToRoute('finish_quiz', [
                'id' => $result->getId(),
            ]);
        } else {
            $question = $questions[$number_of_answered_questions];
            $answers = $question->getAnswers();
        }

        $form = $this->createFormBuilder()
            ->add('1', CheckboxType::class, [
                'required' => false,
            ])
            ->add('2', CheckboxType::class, [
                'required' => false,
            ])
            ->add('3', CheckboxType::class, [
                'required' => false,
            ])
            ->add('submit', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);


        if ($form->get('submit')->isClicked()) {
                $user_answers[0] = $form['1']->getData();
                $user_answers[1] = $form['2']->getData();
                $user_answers[2] = $form['3']->getData();
                if (array_sum($user_answers) == 1) {
                    $array_with_quiz_answers[0] = $answers[0]->getTrueOrNot();
                    $array_with_quiz_answers[1] = $answers[1]->getTrueOrNot();
                    $array_with_quiz_answers[2] = $answers[2]->getTrueOrNot();
                    if ($user_answers === $array_with_quiz_answers) {
                        $result->setRightAnswers($result->getRightAnswers() + 1);
                        $this->addFlash('right', 'Right Answer!');
                    } else
                        $this->addFlash('false', 'Your answer is not right');

                    $result->addQuestion($question);
                    $entityManager = $this->getDoctrine()->getManager();
                    $entityManager->persist($result);
                    $entityManager->flush();
                    return $this->redirectToRoute('playing_quiz_questions', [
                        'id' => $result->getId(),
                    ]);
                }
                else
                    $this->addFlash('One Answer', 'Choose one answer!');
        }
        return $this->render('playing/questions.html.twig', [
            'question' => $question,
            'answers' => $answers,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/finish", name="finish_quiz")
     * @Entity("result", expr="repository.find(id)")
     * @return Response
     */

    public function finishQuiz(Result $result, Request $request): Response
    {

        $quiz = $result->getQuiz();
        $results = $quiz->getResults()->toArray();
        usort($results, function ($result1, $result2){
            if($result1->getRightAnswers() == $result2->getRightAnswers()){
                return 0;
            }
            return ($result1->getRightAnswers() > $result2->getRightAnswers()) ? -1 : 1;
        });
        $places = [];
        $numberOfRightAnswers = [];
        for ($index = 0; $index < count($results); $index++){
            $places[$index] = $results[$index]->getUser();
            $numberOfRightAnswers[$index] = $results[$index]->getRightAnswers();
        }

        foreach ($results as $result)
            if ($result->getUser() == $this->getUser()) {
                $yourPosition = array_search($result, $results) + 1;
            }
        $quiz->setFirstPlace($places[0]->getFirstName() . ' ' . $places[0]->getLastName());
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($quiz);
        $entityManager->flush();
        return $this->render('playing/result_of_quiz.html.twig', [
            'places' => $places,
            'rightAnswers' => $numberOfRightAnswers,
            'yourPosition' => $yourPosition,
        ]);
    }


}


