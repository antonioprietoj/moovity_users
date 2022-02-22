<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class UserNotRepeated extends Constraint
{
    public $message = "{{ errormessage }}";
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }
}
