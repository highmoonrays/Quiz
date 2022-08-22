<?php

declare(strict_types=1);

namespace User\Repository;

use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Mapping\ClassMetadata;
use Laminas\Crypt\Password\Bcrypt;
use Role\Entity\Role;
use User\Entity\User;

class UserRepository extends EntityRepository
{
    private EntityRepository $roleRepository;

    public function __construct(
        EntityManagerInterface $em,
        ClassMetadata $class,
    ) {
        parent::__construct($em, $class);
        $this->roleRepository = $this->getEntityManager()->getRepository(
            Role::class
        );
    }

    /**
     * @param $data
     *
     * @return User
     * @throws \Exception
     */
    public function createUser($data): User
    {
        $timestamp = new DateTime(date('Y-m-d H:i:s'));

        $user = new User();
        $user->setActive(1);
        $user->setUsername($data['username']);
        $hash = (new Bcrypt())->create($data['password']);
        $user->setPassword($hash);
        $user->setEmail($data['email']);
        $user->setGender($data['gender']);
        $user->setCreated($timestamp);
        $user->setUpdated($timestamp);
        $user->setRole(
            $this->roleRepository->findOneBy(
                ['name' => Role::MEMBER_ROLE_NAME])
        );
        $this->getEntityManager()->persist($user);
        $this->getEntityManager()->flush();

        return $user;
    }

    /**
     * @param string $field
     * @param mixed  $value
     *
     * @return bool
     */
    public function isExist(string $field, mixed $value): bool
    {
        return (bool)$this->findOneBy([$field => $value]);
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function validateRegistration(array $data): bool
    {
        return
            !$this->isExist('username', $data['username']) &&
            !$this->isExist('email', $data['email']);
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function validateLogin(array $data): bool
    {
        return $this->isExist('email', $data['email']);
    }
}
