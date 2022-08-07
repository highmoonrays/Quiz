<?php

declare(strict_types=1);

namespace User\Repository;

use Doctrine\ORM\EntityRepository;
use User\Entity\User;

class UserRepository extends EntityRepository
{
    public function getAllUsers()
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('u.email');
        $qb->from(User::class, 'u');
        $qb->getQuery()->execute();

        return $qb;
    }
}