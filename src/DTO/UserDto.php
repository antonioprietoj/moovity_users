<?php

namespace App\DTO;

use App\Entity\User;
use App\Entity\UserGroup;

class UserDto implements \JsonSerializable
{
    private int $id;
    private string $name;
    private array $group;

    private function __construct($id, $name, UserGroup $userGroup = null)
    {
        $this->id = $id;
        $this->name = $name;
        if (isset($userGroup)){
            $this->group = UserGroupDto::fromUserGroup($userGroup)->jsonSerialize();
        }
    }

    static function fromUser(User $user, bool $showGroup = false) :self
    {
        if ($showGroup){
            return new self($user->getId(),$user->getName(),$user->getUserGroup());
        }
        return new self($user->getId(),$user->getName());
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}