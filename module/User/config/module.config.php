<?php

declare(strict_types=1);

namespace User;

use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Laminas\Router\Http\Literal;
use Laminas\ServiceManager\Factory\InvokableFactory;
use User\Controller\Factory\AuthenticationControllerFactory;
use User\Controller\Factory\UserControllerFactory;
use User\Form\Factory\RegistrationFormFactory;
use User\Form\RegistrationForm;

return [
    'form_elements' => [
        'factories' => [
            RegistrationForm::class => RegistrationFormFactory::class
        ]
    ],
    'controllers' => [
        'factories' => [
            Controller\UserController::class => UserControllerFactory::class,
            Controller\AuthenticationController::class => AuthenticationControllerFactory::class
        ],
    ],
    'doctrine' => [
        'driver'          => [
            __NAMESPACE__ => [
                'class' => AttributeDriver::class,
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/Entity'
                ],
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__
                ]
            ]
        ],
    ],
    'router' => [
        'routes' => [
            'register' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/register',
                    'defaults' => [
                        'controller' => Controller\AuthenticationController::class,
                        'action'     => 'register',
                    ],
                ],
            ],
            'login' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/login',
                    'defaults' => [
                        'controller' => Controller\AuthenticationController::class,
                        'action'     => 'login',
                    ],
                ],
            ]
        ],
    ],
    'view_manager' => [
        'template_map' => [
            'authentication/register' => __DIR__ .
                '/../view/user/authentication/register.phtml',
            'login/index' => __DIR__ .
                '/../view/user/authentication/login.phtml',

        ],
        'template_path_stack' => [
            'user' => __DIR__ . '/../view',
        ],
    ],
];
