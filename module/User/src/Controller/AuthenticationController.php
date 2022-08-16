<?php

declare(strict_types=1);

namespace User\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Exception;
use Laminas\Authentication\AuthenticationService;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use User\Entity\User;
use User\Form\LoginForm;
use User\Form\RegistrationForm;
use User\Repository\UserRepository;

class AuthenticationController extends AbstractActionController
{
    private UserRepository $userRepository;
    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {
        $this->userRepository = $this->entityManager->getRepository(
            User::class
        );
    }

    /**
     * @return Response|ViewModel
     */
    public function registerAction(): Response|ViewModel
    {
        $authentication = new AuthenticationService();

        if ($authentication->hasIdentity()) {

            return $this->redirect()->toRoute('home');
        }
        $form = new RegistrationForm($this->userRepository);
        $request = $this->getRequest();

        if ($request->isPost()) {
            $formData = $request->getPost();
            $form->setData($formData);

            if ($form->isValid()) {

                try {
                    $data = $form->getData();

                    if (
                        $this->userRepository->validateUniqueFields($data)
                    ) {
                        $this->userRepository->createUser($data);
                        $this->flashMessenger()->addSuccessMessage(
                            'Account created.'
                        );

                        return $this->redirect()->toRoute('home');
                    }
                } catch (Exception $exception) {
                    $this->flashMessenger()->addSuccessMessage('Something went wrong.');

                    return $this->redirect()->refresh();
                }
            }
        }

        return new ViewModel(['form' => $form]);
    }

    public function loginAction()
    {
        $form = new LoginForm();

        return (new ViewModel(['form' => $form]))->setTemplate('user/authentication/login');

    }
}
