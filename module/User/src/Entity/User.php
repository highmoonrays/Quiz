<?php

declare(strict_types=1);

namespace User\Entity;

use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Index;
use Doctrine\ORM\Mapping\OneToMany;
use Doctrine\ORM\Mapping\Table;
use Role\Entity\Role;
use User\Repository\UserRepository;

#[Entity(repositoryClass: UserRepository::class)]
#[Table('quiz_user')]
#[Index(['id'])]
class User
{
    #[Id]
    #[GeneratedValue]
    #[Column]
    private int $id;

    #[Column(
        name: 'username',
        type: Types::STRING,
        length: 40,
        unique: true,
        nullable: false
    )]
    private string $username;

    #[Column(
        name: 'email',
        type: Types::STRING,
        length: 128,
        unique: true,
        nullable: false
    )]
    private string $email;

    #[Column(type: Types::STRING, length: 80, nullable: false)]
    private string $password;

    #[Column(type: Types::STRING, length: 10, nullable: false)]
    private string $gender;

    #[Column(
        type: Types::STRING,
        length: 128,
        nullable: true,
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

    #[Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private DateTime $created;

    #[Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private DateTime $updated;

    #[OneToMany(mappedBy: 'id', targetEntity: Role::class)]
    private array $roles;

    public const GENDERS = [
        'Female',
        'Male',
        'Other',
    ];

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
     *
     * @return User
     */
    public function setUsername(string $username): self
    {
        $this->username = $username;
        return $this;
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
     *
     * @return User
     */
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
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
     *
     * @return User
     */
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
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
     *
     * @return User
     */
    public function setGender(string $gender): self
    {
        $this->gender = $gender;
        return $this;
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
     *
     * @return User
     */
    public function setPhoto(string $photo): self
    {
        $this->photo = $photo;
        return $this;
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
     *
     * @return User
     */
    public function setActive(int $active): self
    {
        $this->active = $active;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreated(): DateTime
    {
        return $this->created;
    }

    /**
     * @param DateTime $created
     *
     * @return User
     */
    public function setCreated(DateTime $created): self
    {
        $this->created = $created;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getUpdated(): DateTime
    {
        return $this->updated;
    }

    /**
     * @param DateTime $updated
     *
     * @return User
     */
    public function setUpdated(DateTime $updated): self
    {
        $this->updated = $updated;
        return $this;
    }

    /**
     * @return Role[]|null
     */
    public function getRoles(): ?array
    {
        return $this->roles;
    }

    /**
     * @param Role $role
     *
     * @return User
     */
    public function addRole(Role $role): self
    {
        $this->roles[] = $role;
        return $this;
    }
}

