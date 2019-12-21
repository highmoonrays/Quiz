<?php
declare(strict_types=1);
namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Form\AnswersQuestionType;
use App\Form\QuizType;
use App\Repository\QuestionRepository;
use App\Repository\QuizRepository;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
/**
 * @Route("/quiz")
 * @method getId(Request $request)
 * @method addQuiz($quiz)
 * @method addUser(Request $request)
 * @IsGranted("ROLE_ADMIN")
 */
class QuizController extends AbstractController
{
    /**
     * @Route("/", name="quiz_index", methods={"GET"})
     */
    public function index(QuizRepository $quizRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $quizQuery = $quizRepository->findAll();
        $quizzes = $paginator->paginate(
            $quizQuery,
            $request->query->getInt('page',1),
            5
        );
        return $this->render('quiz/index.html.twig', [
            'quizzes' => $quizzes,
        ]);
    }

    /**
     * @Route("/new", name="quiz_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $quiz = new Quiz();
        $quiz->setCreatedAt(new \DateTime());
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
        $questions = $quiz->getQuestions();
        return $this->render('quiz/show.html.twig', [
            'quiz' => $quiz,
            'questions' => $questions,
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
    public function show_all_questions(QuestionRepository $questionRepository, Quiz $quiz,
                                       Request $request, PaginatorInterface $paginator): Response
    {
        $questionsQuery = $questionRepository->findAll();
        $questions = $paginator->paginate(
            $questionsQuery,
            $request->query->getInt('page',1),
            5
        );
        return $this->render('quiz/edit_quiz_questions_show.html.twig', [
            'questions' => $questions,
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
            if ($value == $quiz) {
                $this->addFlash('fail', 'This question is already in the quiz!');
                return $this->redirectToRoute('edit_quiz_questions_show', [
                    'id' => $quiz->getId(),
                ]);
            }
        $quiz->addQuestion($question);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($quiz);
        $entityManager->flush();
        $this->addFlash('added', 'You just add a question to the quiz!');
        return $this->redirectToRoute('edit_quiz_questions_show', [
            'id' => $quiz->getId(),
        ]);
    }

    /**
     * @Route("/{id}/question/create", name="edit_quiz_question_create", methods={"GET","POST"})
     */
    public function create_question(Request $request, Quiz $quiz, QuestionRepository $questionRepository): Response
    {
        $question = new Question();
        $answer1= new Answer();
        $answer2= new Answer();
        $answer3= new Answer();
        $form = $this->createForm(AnswersQuestionType::class, ['question' => $question, 'answer1' => $answer1, 'answer2' => $answer2, 'answer3' => $answer3]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $answer1->setQuestion($question);
            $answer2->setQuestion($question);
            $answer3->setQuestion($question);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($question);
            $entityManager->persist($answer1);
            $entityManager->persist($answer2);
            $entityManager->persist($answer3);
            //checking count of right answers. must be only one
            $array = [];
            $array[1] = $answer1->getTrueOrNot();
            $array[2] = $answer2->getTrueOrNot();
            $array[3] = $answer3->getTrueOrNot();
            if (array_sum($array) == 1) {
                $entityManager->flush();
                return $this->render('quiz/edit_quiz_questions_show.html.twig', [
                    'questions' => $questionRepository->findAll(),
                    'quiz' => $quiz,
                ]);
            }
            else
                $this->addFlash('answers', 'Must be one right answer!');
        }
        return $this->render('quiz/edit_quiz_question_create.html.twig', [
            'quiz' => $quiz,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/{quiz_id}/{id}/edit", name="edit_quiz_question_edit", methods={"GET","POST"})
     * @Entity("quiz", expr="repository.find(quiz_id)")
     * @param Request $request
     * @param Question $question
     * @return Response
     */
    public function edit_question(Request $request, Question $question, Quiz $quiz): Response
    {
        $answers = $question->getAnswers();
        $answer1 = $answers[0];
        $answer2 = $answers[1];
        $answer3 = $answers[2];
        $form = $this->createForm(AnswersQuestionType::class,['question' => $question, 'answer1' => $answer1, 'answer2' => $answer2, 'answer3' => $answer3]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($question);
            //checking count of right answers. must be only one
            $array = [];
            $array[1] = $answer1->getTrueOrNot();
            $array[2] = $answer2->getTrueOrNot();
            $array[3] = $answer3->getTrueOrNot();
            if (array_sum($array) == 1) {
                $entityManager->flush();
                $this->addFlash('success', 'Successfully edited!');
            }
            else
                $this->addFlash('answers', 'Must be one right answer!');
        }
        return $this->render('quiz/edit_quiz_question_edit.html.twig', [
            'quiz' => $quiz,
            'question' => $question,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{quiz_id}/{id}", name="edit_quiz_question_show", methods={"GET"})
     * @Entity("quiz", expr="repository.find(quiz_id)")
     */
    public function show_question(Question $question, Quiz $quiz): Response
    {
        $answers = $question->getAnswers()->toArray();
        return $this->render('quiz/edit_quiz_question_show.html.twig', [
            'quiz' => $quiz,
            'answers' => $answers,
            'question' => $question,
        ]);
    }

}
