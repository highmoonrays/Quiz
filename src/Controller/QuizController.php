<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Form\AnswersQuestionType;
use App\Form\QuizType;
use App\Repository\QuestionRepository;
use App\Repository\QuizRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/quiz")
 * @method getId(Request $request)
 * @method addQuiz($quiz)
 * @method addUser(Request $request)
 */
class QuizController extends AbstractController
{
    /**
     * @Route("/", name="quiz_index", methods={"GET"})
     */
    public function index(QuizRepository $quizRepository): Response
    {
        return $this->render('quiz/index.html.twig', [
            'quizzes' => $quizRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="quiz_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $quiz = new Quiz();
        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($quiz);
            $entityManager->flush();

            return $this->redirectToRoute('quiz_index');
        }

        return $this->render('quiz/new.html.twig', [
            'quiz' => $quiz,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="quiz_show", methods={"GET"})
     */
    public function show(Quiz $quiz): Response
    {
        return $this->render('quiz/show.html.twig', [
            'quiz' => $quiz,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="quiz_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Quiz $quiz): Response
    {
        $form = $this->createForm(QuizType::class, $quiz);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('quiz_index');
        }

        return $this->render('quiz/edit.html.twig', [
            'quiz' => $quiz,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="quiz_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Quiz $quiz): Response
    {
        if ($this->isCsrfTokenValid('delete'.$quiz->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($quiz);
            $entityManager->flush();
        }

        return $this->redirectToRoute('quiz_index');
    }

    /**
     * @Route("/{id}/question", name="edit_quiz_questions_show", methods={"GET"})
     * @param QuestionRepository $questionRepository
     * @return Response
     */
    public function show_questions(QuestionRepository $questionRepository, Quiz $quiz): Response
    {
        return $this->render('quiz/edit_quiz_questions_show.html.twig', [
            'questions' => $questionRepository->findAll(),
            'quiz' => $quiz,
        ]);
    }


    /**
     * @return Response
     * @Route("quiz/{quiz_id}/question/{id}/", name="edit_quiz_question_add", methods={"GET", "POST"})
     * @Entity("quiz", expr="repository.find(quiz_id)")
     */
    public function add_question(Quiz $quiz, Question $question): Response
    {
        foreach ($question->getQuizzes() as $value)
            if ($value == $quiz)
                return $this->redirectToRoute('quiz_index');
        $quiz->addQuestion($question);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($quiz);
        $entityManager->flush();
        return $this->redirectToRoute('quiz_index');
    }

    /**
     * @Route("/{id}/question/create", name="edit_quiz_question_create", methods={"GET","POST"})
     */
    public function create_question(Request $request, Quiz $quiz, QuestionRepository $questionRepository): Response
    {
        $question = new Question();
        $answer= new Answer();
        $form = $this->createForm(AnswersQuestionType::class, ['question' => $question, 'answer' => $answer]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $answer->setQuestion($question);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($question);
            $entityManager->persist($answer);
            $entityManager->flush();

            return $this->render('quiz/edit_quiz_questions_show.html.twig', [
                'questions' => $questionRepository->findAll(),
                'quiz' => $quiz,
            ]);
        }

        return $this->render('quiz/edit_quiz_question_create.html.twig', [
            'quiz' => $quiz,
            'question' => $question,
            'answer' => $answer,
            'form' => $form->createView(),
        ]);
    }

}
