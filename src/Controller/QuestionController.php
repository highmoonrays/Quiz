<?php
declare(strict_types=1);
namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Question;
use App\Form\AnswersQuestionType;
use App\Form\QuestionType;
use App\Repository\QuestionRepository;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/question")
 */
class QuestionController extends AbstractController
{
    /**
     * @Route("/", name="question_index", methods={"GET"})
     * @return Response
     */
    public function index(QuestionRepository $questionRepository): Response
    {
        return $this->render('question/index.html.twig', [
            'questions' => $questionRepository->findAll(),
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
//            $sort_array = $question->getAnswers()->toArray();
//            $answers = ksort($sort_array, 'true_or_not');
//            if (array_sum() == 1){
//                $entityManager->flush();
//                return $this->redirectToRoute('question_index');
//            }
            dump($question);
            dump($question->getAnswers()->toArray());
            die();
        }
        return $this->render('question/new.html.twig', [
            'question' => $question,
            'answer1' => $answer1,
            'answer2' => $answer2,
            'answer3' => $answer3,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="question_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, Question $question): Response
    {
        $form = $this->createForm(QuestionType::class, $question);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('question_index');
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
