<?php

declare(strict_types=1);

namespace App\Validator;

use App\Entity\LearnerGroupInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class NoUsersInSiblingGroupsValidator extends ConstraintValidator
{
    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof NoUsersInSiblingGroups) {
            throw new UnexpectedTypeException($constraint, NoUsersInSiblingGroups::class);
        }

        if (!$value instanceof LearnerGroupInterface) {
            throw new UnexpectedValueException($value, LearnerGroupInterface::class);
        }
        $parent = $value->getParent();
        if (null === $parent) {
            //top level group
            return;
        }

        $siblings = $parent->getChildren()->filter(fn(LearnerGroupInterface $group) => $group !== $value);

        //get all the users in all groups at this level in a flat array
        $siblingUsers = array_merge(
            ...$siblings->map(fn(LearnerGroupInterface $group) => $group->getUsers()->toArray())->toArray()
        );

        foreach ($value->getUsers() as $user) {
            if (in_array($user, $siblingUsers)) {
                $this->context->buildViolation($constraint->message)
                    ->atPath('users')
                    ->addViolation();
                return;
            }
        }
    }
}
