<?php

declare(strict_types=1);

namespace User\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class AuthenticationController extends AbstractActionController
{
    public function createAction()
    {
        return new ViewModel();
    }
}
