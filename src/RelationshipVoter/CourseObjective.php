<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Entity\CourseInterface;
use App\Entity\CourseObjectiveInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class Objective
 */
class CourseObjective extends AbstractVoter
{
    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof CourseObjectiveInterface && in_array($attribute, [
                self::VIEW, self::CREATE, self::EDIT, self::DELETE
            ]);
    }

    /**
     * @param string $attribute
     * @param CourseObjectiveInterface $objective
     * @param TokenInterface $token
     */
    protected function voteOnAttribute($attribute, $objective, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        if ($user->isRoot()) {
            return true;
        }

        switch ($attribute) {
            case self::VIEW:
                return true;
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                /* @var CourseInterface $course */
                $course = $objective->getCourse();
                return $this->permissionChecker->canUpdateCourse($user, $course);
                break;
        }

        return false;
    }
}
