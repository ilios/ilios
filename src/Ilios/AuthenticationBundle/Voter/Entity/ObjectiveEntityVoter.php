<?php

namespace Ilios\AuthenticationBundle\Voter\Entity;

use Ilios\CoreBundle\Entity\CourseInterface;
use Ilios\CoreBundle\Entity\Manager\ProgramYearStewardManager;
use Ilios\CoreBundle\Entity\ObjectiveInterface;
use Ilios\CoreBundle\Entity\ProgramYearInterface;
use Ilios\CoreBundle\Entity\SessionInterface;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class ObjectiveEntityVoter
 */
class ObjectiveEntityVoter extends AbstractVoter
{
    /**
     * @var ProgramYearStewardManager
     */
    protected $stewardManager;

    /**
     * @param ProgramYearStewardManager $stewardManager
     */
    public function __construct(
        ProgramYearStewardManager $stewardManager
    ) {
        $this->stewardManager = $stewardManager;
    }

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

        switch ($attribute) {
            case self::VIEW:
                // Any authenticated user can see all objectives.
                return true;
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                // Well...poop.
                // The rules for granting access hinge on the ownership context of the given objective.
                // Is this a course objective? or a program year object? perhaps a session objective?
                // No easy way of telling.
                // So really, this is three voters in one.
                // TODO: Clean this mess up. [ST 2015/08/05]
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
     * @param SessionUserInterface $sessionUser
     * @return bool
     */
    protected function isCreateEditDeleteGrantedForProgramYearObjective(
        ObjectiveInterface $objective,
        SessionUserInterface $sessionUser
    ) {

        /* @var ProgramYearInterface $programYear */
        $programYear = $objective->getProgramYears()->first(); // there should ever only be one

        // Code below has been copy/pasted straight out of ProgramYearVoter::isGranted().
        // TODO: consolidate. [ST 2015/08/05]
        if ($programYear->isLocked() || $programYear->isArchived()) {
            return false;
        }
        return (
            (
                $sessionUser->hasRole(['Course Director', 'Developer'])
                && (
                    $sessionUser->isThePrimarySchool($programYear->getSchool())
                    || $sessionUser->hasWritePermissionToSchool($programYear->getSchool()->getId())
                    || $this->stewardManager->schoolIsStewardingProgramYear($sessionUser->getSchoolId(), $programYear)
                )
            )
            || $sessionUser->hasWritePermissionToProgram($programYear->getProgram()->getId())
        );
    }

    /**
     * @param ObjectiveInterface $objective
     * @param SessionUserInterface $user
     * @return bool
     */
    protected function isCreateEditDeleteGrantedForSessionObjective(
        ObjectiveInterface $objective,
        SessionUserInterface $sessionUser
    ) {
        /* @var SessionInterface $session */
        $session = $objective->getSessions()->first(); // there should ever only be one

        /* @var CourseInterface $course */
        $course = $session->getCourse();

        // Code below has been copy/pasted straight out of CourseVoter::isGranted().
        // TODO: consolidate. [ST 2015/08/05]
        // HALT!
        // deny DELETE and CREATE privileges if the owning course is locked or archived.
        if ($course->isArchived() || $course->isLocked()) {
            return false;
        }
        return (
            $sessionUser->hasRole(['Faculty', 'Course Director', 'Developer'])
            && (
                $sessionUser->isThePrimarySchool($course->getSchool())
                || $sessionUser->hasWritePermissionToSchool($course->getSchool()->getId())
            )
            || $sessionUser->hasWritePermissionToCourse($course->getId())
        );
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

        // Code below has been copy/pasted straight out of CourseVoter::isGranted().
        // TODO: consolidate. [ST 2015/08/05]
        // HALT!
        // deny DELETE and CREATE privileges if the owning course is locked or archived.
        if ($course->isArchived() || $course->isLocked()) {
            return false;
        }
        return (
            $user->hasRole(['Faculty', 'Course Director', 'Developer'])
            && (
                $user->isThePrimarySchool($course->getSchool())
                || $user->hasWritePermissionToSchool($course->getSchool()->getId())
            )
            || $user->hasWritePermissionToCourse($course->getId())
        );
    }
}
