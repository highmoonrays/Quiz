<?php

declare(strict_types=1);

namespace User\Form;

use Laminas\Form\Element\Checkbox;
use Laminas\Form\Element\Csrf;
use Laminas\Form\Element\Email;
use Laminas\Form\Element\Password;
use Laminas\Form\Element\Submit;
use Laminas\Form\Form;

class LoginForm extends Form
{
    public function __construct($name = null, array $options = [])
    {
        parent::__construct('sign_in');
        $this->setAttribute('method', 'post');

        $this->add(
            [
                'type'       => Email::class,
                'name'       => 'email',
                'options'    => [
                    'label' => 'Email',
                ],
                'attributes' => [
                    'required'     => true,
                    'size'         => 40,
                    'maxLength'    => 128,
                    'pattern'      => '^[a-zA-Z0-9+_.-]+@[a-zA-Z0-9.-]+$',
                    'autocomplete' => false,
                    'data-toggle'  => 'tooltip',
                    'class'        => 'form-control',
                    'title'        => 'Enter email address.',
                    'placeholder'  => 'Email.'

                ]
            ]
        );

        $this->add(
            [
                'type'       => Password::class,
                'name'       => 'password',
                'options'    => [
                    'label' => 'Password'
                ],
                'attributes' => [
                    'required'     => true,
                    'size'         => 40,
                    'maxLength'    => 25,
                    'autocomplete' => false,
                    'data-toggle'  => 'tooltip',
                    'class'        => 'form-control',
                    'title'        => 'Enter password.',
                    'placeholder'  => 'Enter your password.',
                ]
            ]
        );

        $this->add(
            [
                'type'       => Checkbox::class,
                'name'       => 'recall',
                'options'    => [
                    'label'              => 'Remember me?',
                    'label_attributes'   => [
                        'class' => 'custom-control-label'
                    ],
                    'use_hidden_element' => true,
                    'checked_value'      => '1',
                    'unchecked_value'    => '0'
                ],
                'attributes' => [
                    'value' => 0,
                    'id'    => 'recall',
                    'class' => 'form-control-input',
                ]
            ]
        );

        $this->add(
            [
                'type'    => Csrf::class,
                'name'    => 'csrf',
                'options' => [
                    'csrf_options' => [
                        'timeout' => 600,
                    ]
                ]
            ]
        );

        $this->add(
            [
                'type'       => Submit::class,
                'name'       => 'account_login',
                'attributes' => [
                    'value' => 'Account Login',
                    'class' => 'btn btn-primary'
                ]
            ]
        );
    }
}
