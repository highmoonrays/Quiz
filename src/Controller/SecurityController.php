<?php
declare(strict_types=1);
namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Mailer\SentMessage;

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
            if ($this->getDoctrine()->getRepository(User::class)->
            findOneBy(['email' => $form['email']->getData()])) {
                $message = (new \Swift_Message('Reset Password'))
                    ->setFrom('steachy.hm.123@gmail.com')
                    ->setTo('omirom.omirom@gmail.com')
                    ->setBody(
                        $this->renderView('security/reset_password_message.html.twig'), 'text/html'
                    )
                ;
                $mailer->send($message);
                $this->addFlash('success', 'Message has been sent to your email pal');
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
     * @Route("/new-password/{email}/{identifier}", name="new_password", methods={"GET","POST"})
     */
    public function setNewPassword()
    {

    }
}
