<?php

declare(strict_types=1);

namespace User\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\ORM\Mapping\Table;
use Role\Entity\Role;

#[Entity]
#[Table('user')]
#[Index(['id'])]
class User
{
    #[Id]
    #[GeneratedValue]
    #[Column]
    private int $id;

    #[Column(type: Types::STRING, length: 40, unique: true, nullable: false)]
    private string $username;

    #[Column(type: Types::STRING, length: 128, unique: true, nullable: false)]
    private string $email;

    #[Column(type: Types::STRING, length: 80, nullable: false)]
    private string $password;

    #[Column(type: Types::DATE_MUTABLE, nullable: false)]
    private string $birthDay;

    #[Column(type: Types::STRING, length: 10, nullable: false)]
    private string $gender;

    private array $genders = [
        'Female',
        'Male',
        'Other',
    ];

    #[Column(
        type: Types::STRING,
        length: 128,
        nullable: false,
        options: ['default' => 'default.png']
    )]
    private string $photo;

    #[Column(
        type: Types::SMALLINT,
        length: 1,
        nullable: false,
        options: ['default' => 1]
    )]
    private int $active;

    #[Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private string $created;

    #[Column(type: Types::DATETIME_MUTABLE, nullable: false)]
    private string $updated;

    #[OneToOne(inversedBy: 'id', targetEntity: Role::class)]
    private int $role;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return string
     */
    public function getBirthDay(): string
    {
        return $this->birthDay;
    }

    /**
     * @param string $birthDay
     */
    public function setBirthDay(string $birthDay): void
    {
        $this->birthDay = $birthDay;
    }

    /**
     * @return string
     */
    public function getGender(): string
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     */
    public function setGender(string $gender): void
    {
        $this->gender = $gender;
    }

    /**
     * @return array|string[]
     */
    public function getGenders(): array
    {
        return $this->genders;
    }

    /**
     * @return string
     */
    public function getPhoto(): string
    {
        return $this->photo;
    }

    /**
     * @param string $photo
     */
    public function setPhoto(string $photo): void
    {
        $this->photo = $photo;
    }

    /**
     * @return int
     */
    public function getActive(): int
    {
        return $this->active;
    }

    /**
     * @param int $active
     */
    public function setActive(int $active): void
    {
        $this->active = $active;
    }

    /**
     * @return string
     */
    public function getCreated(): string
    {
        return $this->created;
    }

    /**
     * @param string $created
     */
    public function setCreated(string $created): void
    {
        $this->created = $created;
    }

    /**
     * @return string
     */
    public function getUpdated(): string
    {
        return $this->updated;
    }

    /**
     * @param string $updated
     */
    public function setUpdated(string $updated): void
    {
        $this->updated = $updated;
    }

    /**
     * @return int
     */
    public function getRole(): int
    {
        return $this->role;
    }

    /**
     * @param int $role
     */
    public function setRole(int $role): void
    {
        $this->role = $role;
    }

}
