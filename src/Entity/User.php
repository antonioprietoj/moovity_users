<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Validator as AcmeAssert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
/**
 * @AcmeAssert\UserNotRepeated
 */
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255, unique: true, nullable: false)]
    private $name;

    #[ORM\ManyToOne(targetEntity: UserGroup::class, inversedBy: 'users')]
    private $UserGroup;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getUserGroup(): ?UserGroup
    {
        return $this->UserGroup;
    }

    public function setUserGroup(?UserGroup $UserGroup): self
    {
        $this->UserGroup = $UserGroup;

        return $this;
    }
}
