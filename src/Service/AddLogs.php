<?php
namespace App\Service;

use App\Entity\Log;
use App\Entity\UserGroup;
use Doctrine\ORM\EntityManagerInterface;

class AddLogs {
    public const CREATE_USER='CREATE_USER';
    public const DELETE_USER='DELETE_USER';
    public const EDIT_USER='EDIT_USER';
    public const GET_USER='GET_USER';
    public const GET_USERS='GET_USERS';
    public const CREATE_GROUP='CREATE_GROUP';
    public const DELETE_GROUP='DELETE_GROUP';
    public const EDIT_GROUP='EDIT_GROUP';
    public const GET_GROUP='GET_GROUP';
    public const GET_GROUPS='GET_GROUPS';
    public const OTHER_ACTION='OTHER_ACTION';
    private $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    public function addLog($action, $userId = null, $userName = null,  $groupId = null, $groupName = null) :void
    {
        $log = new Log();

        $log->setAction($action);
        $log->setDate(new \DateTime('now'));
        $log->setGroupId($groupId);
        $log->setGroupName($groupName);
        $log->setUserId($userId);
        $log->setUserName($userName);

        $this->saveLog($log);
    }

    public function addLogFromUser($action, $user) :void
    {
        $this->addLog(
            $action,
            $user->getId(),
            $user->getName(),
            $user->getUserGroup()->getId() ?: null,
            $user->getUserGroup()->getName() ?: null
        );
    }

    public function addLogFromGroup($action, UserGroup $group) :void
    {
        $this->addLog(
            $action,
            null,
            null,
            $group->getId(),
            $group->getName()
        );
    }

    public function saveLog($user){
        $this->em->persist($user);
        $this->em->flush();
    }
}