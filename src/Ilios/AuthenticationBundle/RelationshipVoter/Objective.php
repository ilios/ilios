<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use AppBundle\Entity\CourseInterface;
use AppBundle\Entity\ObjectiveInterface;
use AppBundle\Entity\ProgramYearInterface;
use AppBundle\Entity\SessionInterface;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class Objective
 */
class Objective extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof ObjectiveInterface && in_array($attribute, array(
                self::VIEW, self::CREATE, self::EDIT, self::DELETE
            ));
    }

    /**
     * @param string $attribute
     * @param ObjectiveInterface $objective
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $objective, TokenInterface $token)
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
                if (!$objective->getCourses()->isEmpty()) { // got courses? if so, it's a course objective.
                    return $this->isCreateEditDeleteGrantedForCourseObjective($objective, $user);
                } elseif (!$objective->getSessions()->isEmpty()) { // and so on..
                    return $this->isCreateEditDeleteGrantedForSessionObjective($objective, $user);
                } elseif (!$objective->getProgramYears()->isEmpty()) { // and so on ..
                    return $this->isCreateEditDeleteGrantedForProgramYearObjective($objective, $user);
                }
                break;
        }

        return false;
    }

    /**
     * @param ObjectiveInterface $objective
     * @param SessionUserInterface $user
     * @return bool
     */
    protected function isCreateEditDeleteGrantedForProgramYearObjective(
        ObjectiveInterface $objective,
        SessionUserInterface $user
    ) {

        /* @var ProgramYearInterface $programYear */
        $programYear = $objective->getProgramYears()->first(); // there should ever only be one

        return $this->permissionChecker->canUpdateProgramYear($user, $programYear);
    }

    /**
     * @param ObjectiveInterface $objective
     * @param SessionUserInterface $user
     * @return bool
     */
    protected function isCreateEditDeleteGrantedForSessionObjective(
        ObjectiveInterface $objective,
        SessionUserInterface $user
    ) {
        /* @var SessionInterface $session */
        $session = $objective->getSessions()->first(); // there should ever only be one

        return $this->permissionChecker->canUpdateSession($user, $session);
    }

    /**
     * @param ObjectiveInterface $objective
     * @param SessionUserInterface $user
     * @return bool
     */
    protected function isCreateEditDeleteGrantedForCourseObjective($objective, $user)
    {
        /* @var CourseInterface $course */
        $course = $objective->getCourses()->first(); // there should ever only be one

        return $this->permissionChecker->canUpdateCourse($user, $course);
    }
}
