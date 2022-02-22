<?php

namespace App\Validator;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class UserNotRepeatedValidator extends ConstraintValidator
{
    private $userRepo;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepo = $userRepository;
    }

    /**
     * @param User $userEnter
     */
    public function validate($userEnter, Constraint $constraint)
    {
        $users = $this->userRepo->findAll();
        $errorstatus = false;
        $errormessage = '';

        foreach ($users as $user){
            if (empty($userEnter->getName())){
                $errorstatus = true;
                $errormessage = "Invalid username.";
                break;
            }

            if ($user->getId() != $userEnter->getId()) {
                if (strtolower($user->getName()) == strtolower($userEnter->getName()))
                {
                    $errorstatus = true;
                    $errormessage = sprintf("User '%s' already exists.",$user->getName());
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
