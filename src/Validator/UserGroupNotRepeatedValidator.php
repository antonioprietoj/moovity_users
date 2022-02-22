<?php

namespace App\Validator;

use App\Entity\UserGroup;
use App\Repository\UserGroupRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UserGroupNotRepeatedValidator extends ConstraintValidator
{
    private $userGroupRepo;

    public function __construct(UserGroupRepository $userGroupRepository)
    {
        $this->userGroupRepo = $userGroupRepository;
    }

    /**
     * @param UserGroup $userGroupEnter
     */
    public function validate($userGroupEnter, Constraint $constraint)
    {
        $groups = $this->userGroupRepo->findAll();
        $errorstatus = false;
        $errormessage = '';

        foreach ($groups as $group){
            if ($group->getId() != $userGroupEnter->getId()) {
                if (strtolower($group->getName()) == strtolower($userGroupEnter->getName()))
                {
                    $errorstatus = true;
                    $errormessage = sprintf("UserGroup '%s' already exists.",$group->getName());
                    break;
                }
                if (empty($userGroupEnter->getName())){
                    $errorstatus = true;
                    $errormessage = "Invalid group-name.";
                    break;
                }
            }
        }

        if(!$errorstatus){
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ errormessage }}', $errormessage)
            ->addViolation();
    }
}
