<?php

declare(strict_types=1);

namespace User\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use User\Repository\UserRepository;

class UserController extends AbstractActionController
{
    public function __construct(
        private readonly UserRepository $userRepository
    ) {}

    public function indexAction()
    {
        $user = $this->userRepository->findOneBy(['id' => 1]);
        var_dump($user->getId());
        exit;
    }
}