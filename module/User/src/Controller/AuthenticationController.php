<?php

declare(strict_types=1);

namespace User\Controller;

use CwBase\Helper\HashHelper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Exception;
use Laminas\Authentication\Adapter\DbTable\CredentialTreatmentAdapter;
use Laminas\Authentication\AuthenticationService;
use Laminas\Authentication\Result;
use Laminas\Crypt\Password\Bcrypt;
use Laminas\Db\Adapter\Adapter;
use Laminas\Http\Response;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\Session\SessionManager;
use Laminas\View\Model\ViewModel;
use User\Entity\User;
use User\Form\LoginForm;
use User\Form\RegistrationForm;
use User\Repository\UserRepository;

class AuthenticationController extends AbstractActionController
{
    private EntityRepository $userRepository;

    /**
     * @param EntityManagerInterface $entityManager
     * @param AuthenticationService  $authenticationService
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly AuthenticationService $authenticationService
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
        if ($this->authenticationService->hasIdentity()) {

            return $this->redirect()->toRoute('home');
        }
        $form = new RegistrationForm();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $formData = $request->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                try {
                    $data = $form->getData();

                    if (
                        $this->userRepository->validateRegistration($data)
                    ) {
                        $this->userRepository->createUser($data);
                        $this->flashMessenger()->addSuccessMessage(
                            'Account created.'
                        );

                        return $this->redirect()->toRoute('login');
                    }
                } catch (Exception $exception) {
                    $this->flashMessenger()->addSuccessMessage(
                        'Something went wrong.'
                    );

                    return $this->redirect()->refresh();
                }
            }
        }

        return new ViewModel(['form' => $form]);
    }

    /**
     * @return Response|ViewModel
     */
    public function loginAction(): Response|ViewModel
    {
        if ($this->authenticationService->hasIdentity()) {

//            return $this->redirect()->toRoute('home');
        }
        $form = new LoginForm();
        $request = $this->getRequest();

        if ($request->isPost()) {
            $formData = $request->getPost();
            $form->setData($formData);

            if ($form->isValid()) {
                try {
                    $data = $form->getData();
                    $user = $this->userRepository->findOneBy(
                        ['email' => $data['email']]
                    );

                    if (
                        $user
                    ) {
                        $adapter = $this->authenticationService->getAdapter();
                        $adapter->setIdentity($user->getEmail());
                        $hash = new Bcrypt();

                        if (
                            $hash->verify(
                                $data['password'],
                                $user->getPassword()
                            )
                        ) {
                            $adapter->setCredential($user->getPassword());
                            $result =
                                $this->authenticationService->authenticate();

                            switch ($result->getCode()) {
                                case Result::FAILURE_IDENTITY_NOT_FOUND:
                                    $this->flashMessenger()->addErrorMessage(
                                        'Incorrect Email.'
                                    );

                                    return $this->redirect()->refresh();

                                case Result::FAILURE_CREDENTIAL_INVALID:
                                    $this->flashMessenger()->addErrorMessage(
                                        'Incorrect Password.'
                                    );

                                    return $this->redirect()->refresh();

                                case Result::SUCCESS:

                                    if ($data['recall'] === 1) {
                                        $sessionManager = new SessionManager();
                                        $ttl = 1814400;
                                        $sessionManager->rememberMe($ttl);
                                    }

                                    return $this->redirect()->toRoute(
                                        'profile',
                                        [
                                            'id' => $user->getId(),
                                            'username' => $user->getUsername(),
                                        ]
                                    );

                                default:
                                    $this->flashMessenger()->addErrorMessage(
                                        'Authentication Failure.'
                                    );

                                    return $this->redirect()->refresh();
                            }
                        } else {
                            $adapter->setCredential('');
                        }
                    }
                } catch (Exception $exception) {
                    $this->flashMessenger()->addSuccessMessage(
                        'Something went wrong.'
                    );

                    return $this->redirect()->refresh();
                }
            }
        }

        return (new ViewModel(['form' => $form]))->setTemplate(
            'user/authentication/login'
        );

    }

}

