<?php

declare(strict_types=1);

namespace App\Validator;

use App\Entity\LearnerGroupInterface;
use App\Repository\LearnerGroupRepository;
use ReflectionProperty;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class NoUsersInSiblingGroupsValidator extends ConstraintValidator
{
    public function __construct(
        private readonly LearnerGroupRepository $learnerGroupRepository,
    ) {
    }

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

        $usersBySiblingGroup = $this->learnerGroupRepository->getChildUsersInGroup($parent->getId());

        //we use this validator on unpersisted groups, so we need to check that the ID exists before accessing it
        $idProperty = new ReflectionProperty($value, 'id');
        if ($idProperty->isInitialized($value)) {
            unset($usersBySiblingGroup[$value->getId()]);
        }

        $siblingUsers = $usersBySiblingGroup === []
            ? []
            : array_merge(...array_values($usersBySiblingGroup));

        foreach ($value->getUsers() as $user) {
            if (in_array($user->getId(), $siblingUsers, true)) {
                $this->context->buildViolation($constraint->message)
                    ->atPath('users')
                    ->addViolation();
                return;
            }
        }
    }
}
