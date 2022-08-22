<?php

declare(strict_types=1);

namespace User\Controller\Factory;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\Authentication\AuthenticationService;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Controller\AuthenticationController;

class AuthenticationControllerFactory implements FactoryInterface
{

    /**
     * @param ContainerInterface $container
     * @param                    $requestedName
     * @param array|null         $options
     *
     * @return AuthenticationController
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null
    ): AuthenticationController {
        return new AuthenticationController(
            $container->get(EntityManager::class),
            $container->get('doctrine.authenticationservice.orm_default'),
        );
    }

}

