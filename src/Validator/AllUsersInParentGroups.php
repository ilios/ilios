<?php

declare(strict_types=1);

namespace App\Validator;

use Attribute;
use Symfony\Component\Validator\Constraint;

#[Attribute]
class AllUsersInParentGroups extends Constraint
{
    public string $message = 'Every user in a learner group must also be a user in its parent group.';

    public function getTargets(): string
    {
        return self::CLASS_CONSTRAINT;
    }
}
