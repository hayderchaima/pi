<?php

namespace App\Entity;


use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * @Annotation
 */
class NotFutureDate extends Constraint
{
    public string $message = 'La date ne peut pas être dans le futur.';
}


