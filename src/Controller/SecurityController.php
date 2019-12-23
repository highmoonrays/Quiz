<?php
declare(strict_types=1);
namespace App\Controller;

use App\Entity\User;
use App\Form\NewPasswordType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Entity;


class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
             return $this->redirectToRoute('main_page');
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout()
    {
        return $this->redirectToRoute('app_login');
    }

    /**
     * @Route("/reset-password", name="reset_password", methods={"GET", "POST"})
     * @throws \Symfony\Component\Mailer\Exception\TransportExceptionInterface
     */
    public function resetPassword(Request $request, UserRepository $userRepository, \Swift_Mailer $mailer): Response
    {
        $form = $this->createFormBuilder()
            ->add('email', EmailType::class)
            ->add('submit', SubmitType::class)
            ->getForm();
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {
            $email = $form['email']->getData();
            $user = $this->getDoctrine()->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($user) {


                $identifier = random_int(1, 1000);
                $message = (new \Swift_Message('Reset Password'))
                    ->setFrom('quiz-reset-password@quiz.local')
                    ->setTo($email)
                    ->setBody(
                        $this->renderView('security/reset_password_message.html.twig', [
                            'email' => $email,
                            'identifier' => $identifier,
                            'id' => $user->getId(),
                        ]), 'text/html'
                    );


                $mailer->send($message);
                $this->addFlash('success', 'Message has been sent to your email pal');
                return $this->redirectToRoute('new_password', [
                    'email' => $form['email']->getData(),
                    'identifier' => $identifier,
                    'id' => $user->getId(),
                ]);
            }
            else {
                $this->addFlash('wrong', 'Did you wrote correct email?');
            }
        }
        return $this->render('security/reset_password.html.twig',[
                'form' => $form->createView(),
            ]);
    }

    /**
     * @Route("/new-password/{email}/{identifier}/{id}", name="new_password", methods={"GET","POST"})
     * @Entity("user", expr="repository.find(id)")
     * @param UserPasswordEncoderInterface $passwordEncoder
     */
    public function setNewPassword(Request $request,User $user, UserPasswordEncoderInterface $passwordEncoder): Response
    {
        $form = $this->createForm(NewPasswordType::class);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){
                $user->setPassword(
                    $passwordEncoder->encodePassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($user);
                $entityManager->flush();
                $this->addFlash('new password', 'You just changed the password!');
                return $this->redirectToRoute('app_login');
            }
            return $this->render('security/new_password.html.twig', [
                'form' => $form->createView(),
            ]);
    }
}
