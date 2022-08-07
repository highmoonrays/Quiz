<?php

declare(strict_types=1);

namespace User\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use User\Entity\User;

class UserController extends AbstractActionController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function indexAction()
    {
        $user = $this->entityManager->getRepository(User::class)
            ->getAllUsers();
        var_dump($user);
        exit;
    }
}