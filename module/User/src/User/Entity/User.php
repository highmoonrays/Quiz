<?php

namespace User\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity]
#[Table('user')]
class User
{
    #[Id]
    #[Column]
    private int $id;

    #[Column(type: Types::STRING)]
    private string $name;

}