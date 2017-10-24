<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\CourseInterface;
use Ilios\CoreBundle\Entity\DTO\CourseDTO;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class Course extends Voter
{
    /**
     * @var string
     */
    const VIEW = 'view';

    /**
     * @var string
     */
    const EDIT = 'edit';

    /**
     * @var string
     */
    const DELETE = 'delete';

    /**
     * @var string
     */
    const CREATE = 'create';

    protected function supports($attribute, $subject)
    {
        return (
            $subject instanceof CourseDTO && in_array($attribute, [self::VIEW]) or
            $subject instanceof CourseInterface && in_array($attribute, [
                self::CREATE, self::VIEW, self::EDIT, self::DELETE
            ])
        );
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }
        if ($user->isRoot()) {
            return true;
        }

        if ($subject instanceof CourseDTO) {
            return $this->voteOnDTO($user, $subject);
        }

        if ($subject instanceof CourseInterface) {
            return $this->voteOnEntity($attribute, $user, $subject);
        }

        return false;
    }

    protected function voteOnDTO(SessionUserInterface $sessionUser, CourseDTO $course): bool
    {
        return $sessionUser->canReadCourse($course->id, $course->school);
    }

    protected function voteOnEntity(string $attribute, SessionUserInterface $sessionUser, CourseInterface $course): bool
    {
        switch ($attribute) {
            case self::VIEW:
                return $sessionUser->canReadCourse($course->getId(), $course->getSchool()->getId());
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                throw new \Exception($attribute . ' attribute not implemented yet');
                break;
        }

        return false;
    }
}