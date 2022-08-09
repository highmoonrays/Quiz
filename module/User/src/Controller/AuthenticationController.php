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
     * @return ViewModel
     */
    public function registerAction(): ViewModel
    {
        $authentication = new AuthenticationService();

//        if (!$authentication->hasIdentity()) {
//            return $this->redirect()->toRoute('home');
//        }
        $form = new RegistrationForm($this->userRepository);
        $request = $this->getRequest();

        if ($request->isPost()) {
            $formData = $request->getPost()->toArray();
            $form->setData($formData);

            if ($form->isValid()) {
                try {
                    $data = $form->getData();
                    $this->userRepository->createUser($data);
                    $this->flushMessenger()->addSuccessMessage('Account created.');
                    return $this->redirect()->toRoute('home'); //todo add a
                    // redirect to the quiz home page
                } catch (Exception $exception) {
                    $this->flushMessenger()->addSuccessMessage('Something went wrong.');
                    return $this->redirect()->refresh();
                }
            }
        }
        return new ViewModel(['form' => $form]);
    }
}
