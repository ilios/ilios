<?php

declare(strict_types=1);

namespace App\Validator;

use App\Entity\LearnerGroupInterface;
use App\Repository\LearnerGroupRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class AllUsersInParentGroupsValidator extends ConstraintValidator
{
    public function __construct(
        private readonly LearnerGroupRepository $learnerGroupRepository,
    ) {
    }

    public function validate(mixed $value, Constraint $constraint): void
    {
        if (!$constraint instanceof AllUsersInParentGroups) {
            throw new UnexpectedTypeException($constraint, AllUsersInParentGroups::class);
        }

        if (!$value instanceof LearnerGroupInterface) {
            throw new UnexpectedValueException($value, LearnerGroupInterface::class);
        }
        $parent = $value->getParent();
        if (null === $parent) {
            //top level group
            return;
        }

        $parentUsers = $this->learnerGroupRepository->getUsersIdsInGroup($parent->getId());

        foreach ($value->getUsers() as $user) {
            if (!in_array($user->getId(), $parentUsers, true)) {
                $this->context
                    ->buildViolation($constraint->message)
                    ->atPath('users')
                    ->addViolation();
                return;
            }
        }
    }
}
