<?php

declare(strict_types=1);

namespace User;

use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Laminas\Router\Http\Literal;
use User\Controller\Factory\UserControllerFactory;

return [
    'controllers' => [
        'factories' => [
            Controller\UserController::class => UserControllerFactory::class,
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
            'user' => [
                'type'    => Literal::class,
                'options' => [
                    'route'    => '/user',
                    'defaults' => [
                        'controller' => Controller\UserController::class,
                        'action'     => 'index',
                    ],
                ],
            ]
        ],
    ],
];
