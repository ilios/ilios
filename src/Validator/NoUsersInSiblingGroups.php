<?php

declare(strict_types=1);

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class NoUsersInSiblingGroups extends Constraint
{
    public string $message = 'A user cannot be added to more than one sibling group.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
