<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\DTO\CourseDTO;
use Ilios\AuthenticationBundle\Voter\CourseVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class CourseDTOVoter
 */
class CourseDTOVoter extends CourseVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof CourseDTO && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param CourseDTO $course
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $course, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return $this->isViewGranted($course->id, $course->school, $user);
                break;
        }
        return false;
    }
}
