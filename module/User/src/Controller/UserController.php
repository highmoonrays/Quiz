<?php

declare(strict_types=1);

namespace User\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Laminas\Mvc\Controller\AbstractActionController;

class UserController extends AbstractActionController
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function indexAction()
    {
        exit;
    }
}
