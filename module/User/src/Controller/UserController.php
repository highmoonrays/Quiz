<?php

declare(strict_types=1);

namespace User\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class UserController extends AbstractActionController
{
    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    /**
     * @return ViewModel
     */
    public function indexAction()
    {
        return new ViewModel();
    }
}
