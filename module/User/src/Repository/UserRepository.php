<?php

declare(strict_types=1);

namespace User\Repository;

use DateTime;
use Doctrine\ORM\EntityRepository;
use Laminas\Crypt\Password\Bcrypt;
use User\Entity\User;

class UserRepository extends EntityRepository
{
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
    public function isUniqueValue(string $field, mixed $value): bool
    {
        return (bool)$this->findOneBy([$field => $value]);
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function validateUniqueFields(array $data): bool
    {
        return
            !$this->isUniqueValue('username', $data['username']) &&
            !$this->isUniqueValue('email', $data['email']);
    }
}
