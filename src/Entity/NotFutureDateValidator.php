<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

class NotFutureDateValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof NotFutureDate) {
            throw new UnexpectedTypeException($constraint, NotFutureDate::class);
        }

        // Vérifie si la date est dans le futur
        if ($value > new \DateTime()) {
            $this->context->buildViolation($constraint->message)
                ->addViolation();
        }
    }
}
