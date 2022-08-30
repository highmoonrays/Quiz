<?php

declare(strict_types=1);

namespace User;

use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Laminas\Router\Http\Literal;
use Laminas\Router\Http\Segment;
use Laminas\ServiceManager\Factory\InvokableFactory;
use User\Controller\Factory\AuthenticationControllerFactory;
use User\Controller\Factory\UserControllerFactory;
use User\Form\Factory\RegistrationFormFactory;
use User\Form\RegistrationForm;

return [
    'form_elements' => [
        'factories' => [
            RegistrationForm::class => InvokableFactory::class
        ]
    ],
    'controllers' => [
        'factories' => [
            Controller\UserController::class => UserControllerFactory::class,
            Controller\AuthenticationController::class => AuthenticationControllerFactory::class,
            Controller\ProfileController::class => InvokableFactory::class
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
        'authentication' => [
            'orm_default' => [
                'object_manager' => 'Doctrine\ORM\EntityManager',
                'identity_class' => 'User\Entity\User',
                'identity_property' => 'email',
                'credential_property' => 'password',
            ],
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
            ],
            'profile' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/profile[/:id[/:username]]',
                    'constraints' => [
                        'id' => '[0-9]+',
                        'username' => '[a-zA-Z][a-zA-Z0-9_-]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\ProfileController::class,
                        'action'     => 'profile',
                    ],
                ],
            ],
            'logout' => [
                'type'    => Segment::class,
                'options' => [
                    'route'    => '/logout',
                    'constraints' => [
                        'id' => '[0-9]+',
                        'username' => '[a-zA-Z][a-zA-Z0-9_-]+',
                    ],
                    'defaults' => [
                        'controller' => Controller\AuthenticationController::class,
                        'action'     => 'logout',
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
            'profile/profile' => __DIR__ .
                '/../view/user/profile/profile.phtml',
        ],
        'template_path_stack' => [
            'user' => __DIR__ . '/../view',
        ],
    ],
];
