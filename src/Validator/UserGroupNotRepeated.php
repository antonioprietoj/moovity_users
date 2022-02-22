<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UserGroupNotRepeated extends Constraint
{
    public $message = "{{ errormessage }}";
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
