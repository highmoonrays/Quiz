<?php

declare(strict_types=1);

namespace User;

use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Laminas\Router\Http\Literal;
use Laminas\ServiceManager\Factory\InvokableFactory;
use User\Controller\Factory\UserControllerFactory;

return [
    'controllers' => [
        'factories' => [
            Controller\UserController::class => UserControllerFactory::class,
            Controller\AuthenticationController::class => InvokableFactory::class
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
            ]
        ],
    ],
    'view_manager' => [
        'template_map' => [
            'authentication/create' => __DIR__ .
                '/../view/user/authentication/register.phtml',

        ],
        'template_path_stack' => [
            'user' => __DIR__ . '/../view',
        ],
    ],
];
