<?php
declare(strict_types=1);
namespace App\Controller;

use App\Entity\User;
use App\Form\SearchBarType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @param UserRepository $userRepository
     * @param PaginatorInterface $paginator
     * @return Response
     * @Route("/", name="user_index", methods={"GET", "POST"})
     */
    public function index(UserRepository $userRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $form = $this->createForm(SearchBarType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $query = $form['query']->getData();
            $usersQuery = $userRepository->findUsersByEmailField($query);
        }
        else {
            $usersQuery = $userRepository->findAll();
        }
        $users = $paginator->paginate(
            $usersQuery,
            $request->query->getInt('page',1),
            5
        );

        return $this->render('user/index.html.twig', [
            'users' => $users,
            'searchBar' => $form->createView(),
        ]);
    }

    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/show/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"DELETE"})
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }

    /**
     * @Route("/show/change/{id}", name="user_change_role", methods={"GET","POST"})
     */
    public function change_role(Request $request, User $user, UserRepository $userRepository): Response
    {
        if($user->getRoles() == ['ROLE_USER'])
            $user->setRoles(['ROLE_ADMIN']);
        elseif ($user->getRoles() == ['ROLE_ADMIN', 'ROLE_USER'])
            $user->setRoles(['ROLE_USER']);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        return $this->redirectToRoute('user_index', [
            'users' => $userRepository->findAll(),
        ]);
    }
}
