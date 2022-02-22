<?php
namespace App\Service;

use App\DTO\UserGroupDto;
use App\Entity\UserGroup;
use App\Exception\ValidatorException;
use App\Repository\UserGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AddUserGroups {

    private $em;
    private $validator;
    private $userGroupRepo;
    private $addLogs;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validatorInterface,
        UserGroupRepository $userGroupRepository,
        AddLogs $addLogs
    )
    {
        $this->em = $entityManager;
        $this->validator = $validatorInterface;
        $this->userGroupRepo = $userGroupRepository;
        $this->addLogs = $addLogs;
    }

    public function addUserGroup($name) :UserGroup
    {
        $userGroup = new UserGroup();

        $userGroup->setName(trim($name));

        $this->validateUserGroup($userGroup);
        $this->saveUserGroup($userGroup);
        $this->addLogs->addLogFromGroup($this->addLogs::CREATE_GROUP, $userGroup);

        return $userGroup;
    }

    public function validateUserGroup(UserGroup $userGroup){
        $errors= $this->validator->validate($userGroup);
        if ($errors->count()){
            throw new ValidatorException($errors->get(0)->getMessage());
        }
    }

    public function editUserGroup(UserGroup $userGroup, $name) :UserGroup
    {
        $userGroup->setName(trim($name));

        $this->validateUserGroup($userGroup);
        $this->saveUserGroup($userGroup);
        $this->addLogs->addLogFromGroup($this->addLogs::EDIT_GROUP, $userGroup);

        return $userGroup;
    }

    public function removeUserGroup(UserGroup $userGroup) :void
    {
        $this->addLogs->addLogFromGroup($this->addLogs::DELETE_GROUP, $userGroup);
        foreach ($userGroup->getUsers() as $user){
            $userGroup->removeUser($user);
        }

        $this->em->remove($userGroup);
        $this->em->flush();
    }

    public function saveUserGroup(UserGroup $userGroup){
        $this->em->persist($userGroup);
        $this->em->flush();
    }

    public  function getUserGroups(){
        $userGroups = $this->userGroupRepo->findAll();

        $userGroupsArray = array_map(
            function ($userGroup){
                return UserGroupDto::fromUserGroup($userGroup,true);
            },
            $userGroups
        );

        $this->addLogs->addLog($this->addLogs::GET_GROUPS);
        return $userGroupsArray;
    }

    public  function getUserGroup(UserGroup $userGroup){
        $this->addLogs->addLog($this->addLogs::GET_GROUP);
        return UserGroupDto::fromUserGroup($userGroup,true);
    }
}