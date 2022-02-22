<?php
namespace App\Service;

use App\DTO\UserDto;
use App\Entity\User;
use App\Exception\ValidatorException;
use App\Repository\UserGroupRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class AddUsers {

    private $em;
    private $validator;
    private $userRepo;
    private $userGroupRepo;
    private $addLogs;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface $validatorInterface,
        UserRepository $userRepository,
        UserGroupRepository $userGroupRepository,
        AddLogs $addLogs
    )
    {
        $this->em = $entityManager;
        $this->validator = $validatorInterface;
        $this->userRepo = $userRepository;
        $this->userGroupRepo = $userGroupRepository;
        $this->addLogs = $addLogs;
    }

    public function addUser($name, $idGroup) :User
    {
        $user = new User();

        $user->setName(trim($name));

        if ($group = $this->userGroupRepo->find($idGroup)){
            $user->setUserGroup($group);
        }

        $this->validateUser($user);
        $this->saveUser($user);
        $this->addLogs->addLogFromUser($this->addLogs::CREATE_USER, $user);

        return $user;
    }

    public function editUser(User $user, $name, $idGroup) :User
    {
        $user->setName(trim($name));

        if ($idGroup){
            $group = $this->validateUserGroupExists($idGroup);
            $user->setUserGroup($group);
        }

        $this->validateUser($user);
        $this->saveUser($user);
        $this->addLogs->addLogFromUser($this->addLogs::EDIT_USER, $user);

        return $user;
    }

    public function removeUser(User $user) :void
    {
        $this->addLogs->addLogFromUser($this->addLogs::DELETE_USER, $user);
        $this->em->remove($user);
        $this->em->flush();
    }

    public function saveUser($user){
        $this->em->persist($user);
        $this->em->flush();
    }

    public  function getUsers(){
        $users = $this->userRepo->findAll();

        $usersArray = array_map(
            function ($user){
                return UserDto::fromUser($user, true);
            },
            $users
        );

        $this->addLogs->addLog($this->addLogs::GET_USERS);
        return $usersArray;
    }

    public  function getUser(User $user){
        $this->addLogs->addLog($this->addLogs::GET_USER);
        return UserDto::fromUser($user,true);
    }

    public function validateUser(User $user){
        $errors= $this->validator->validate($user);
        if ($errors->count()){
            throw new ValidatorException($errors->get(0)->getMessage());
        }
    }

    public function validateUserGroupExists($idGroup){
        if ($group = $this->userGroupRepo->find($idGroup)){
            return $group;
        }
            throw new ValidatorException(sprintf("This group '%s' not exists.", $idGroup));
    }
}