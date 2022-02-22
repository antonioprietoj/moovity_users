<?php

namespace App\DTO;

use App\Entity\User;
use App\Entity\UserGroup;

class UserGroupDto implements \JsonSerializable
{
    private int $id;
    private string $name;
    private int $totalUsers;
    private array $users;

    private function __construct($id, $name, $users = null)
    {
        $this->id = $id;
        $this->name = $name;
        if (isset($users)){
            $usersArray = array_map(
                function ($user){
                    return UserDto::fromUser($user);
                },
                $users->toArray()
            );

            $this->users = $usersArray;
            $this->totalUsers = $users->count();
        }
    }

    static function fromUserGroup(UserGroup $userGroup, bool $showUsers = false) :self
    {
        if ($showUsers){
            return new self($userGroup->getId(),$userGroup->getName(),$userGroup->getUsers());
        }
        return new self($userGroup->getId(),$userGroup->getName());
    }

    public function jsonSerialize()
    {
        return get_object_vars($this);
    }
}