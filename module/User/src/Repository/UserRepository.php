<?php

declare(strict_types=1);

namespace User\Repository;

use Doctrine\ORM\EntityRepository;

class UserRepository extends EntityRepository
{
    public function createUser($data)
    {
        var_dump($data);
    }
}
