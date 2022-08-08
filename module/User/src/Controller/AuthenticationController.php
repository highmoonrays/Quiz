<?php

declare(strict_types=1);

namespace User\Controller;

use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;
use User\Form\RegistrationForm;

class AuthenticationController extends AbstractActionController
{
    public function registerAction()
    {
        $form = new RegistrationForm();
        return new ViewModel(['form' => $form]);
    }
}
