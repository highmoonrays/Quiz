<?php
declare(strict_types=1);
namespace App\Controller;

use App\Entity\Quiz;
use App\Form\SearchBarType;
use App\Repository\QuizRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/main", name="main_page", methods={"GET", "POST"})
     */
    public function main(QuizRepository $quizRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $form = $this->createForm(SearchBarType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $query = $form['query']->getData();
            $quizzesQuery = $quizRepository->findQuizzesByName($query);
        }
        else {
            $quizzesQuery = $quizRepository->findAll();
        }
        $activeQuizzes = [];
        foreach ($quizzesQuery as $quiz)
            if($quiz->getStatus() == true)
                 array_push($activeQuizzes, $quiz);
        $quizzes = $paginator->paginate(
            $activeQuizzes,
            $request->query->getInt('page',1),
            7
        );

        return $this->render('main/main_page.html.twig', [
            'quizzes' => $quizzes,
            'searchBar' => $form->createView(),
        ]);
    }
}
