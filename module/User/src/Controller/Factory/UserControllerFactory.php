<?php

declare(strict_types=1);

namespace User\Controller\Factory;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Controller\UserController;
use User\Entity\User;
use User\Repository\UserRepository;

class UserControllerFactory implements FactoryInterface
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null
    ): UserController {
        return new UserController(
            $container->get(EntityManager::class)
        );
    }
}
