<?php
declare(strict_types=1);
namespace App\Controller;

use App\Entity\Answer;
use App\Form\AnswersQuestionType;
use App\Entity\Question;
use App\Repository\QuestionRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/question")
 * @IsGranted("ROLE_ADMIN")
 */
class QuestionController extends AbstractController
{
    /**
     * @Route("/", name="question_index", methods={"GET"})
     * @return Response
     */
    public function index(QuestionRepository $questionRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $questionsQuery = $questionRepository->findAll();
        $questions = $paginator->paginate(
            $questionsQuery,
            $request->query->getInt('page',1),
            5
        );
        return $this->render('question/index.html.twig', [
            'questions' => $questions,
        ]);
    }

    /**
     * @Route("/new", name="question_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
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
                return $this->redirectToRoute('question_index');
            }
            else
                $this->addFlash('answers', 'Must be one right answer!');
        }
        return $this->render('question/new.html.twig', [
            'question' => $question,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="question_show", methods={"GET"})
     */
    public function show(Question $question): Response
    {
        $answers = $question->getAnswers()->toArray();
        return $this->render('question/show.html.twig', [
            'answers' => $answers,
            'question' => $question,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="question_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Question $question
     * @return Response
     */
    public function edit(Request $request, Question $question,QuestionRepository $questionRepository): Response
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
        return $this->render('question/edit.html.twig', [
            'question' => $question,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="question_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Question $question): Response
    {
        if ($this->isCsrfTokenValid('delete'.$question->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($question);
            $entityManager->flush();
        }

        return $this->redirectToRoute('question_index');
    }
}
