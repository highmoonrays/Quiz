<?php

declare(strict_types=1);

namespace User\Form\Factory;

use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use User\Form\RegistrationForm;
use User\Repository\UserRepository;

class RegistrationFormFactory implements FactoryInterface
{
    /**
     * @param ContainerInterface $container
     * @param                    $requestedName
     * @param array|null         $options
     *
     * @return RegistrationForm
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        ?array $options = null
    ): RegistrationForm {
        /** @var EntityManager $entityManager */
        $entityManager = $container->get(EntityManager::class);
        return new RegistrationForm(
            $entityManager->getRepository(UserRepository::class)
        );
    }
}
