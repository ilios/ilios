<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\CourseInterface;
use Ilios\CoreBundle\Entity\Manager\PermissionManagerInterface;
use Ilios\CoreBundle\Entity\Manager\ProgramYearStewardManagerInterface;
use Ilios\CoreBundle\Entity\ObjectiveInterface;
use Ilios\CoreBundle\Entity\ProgramYearInterface;
use Ilios\CoreBundle\Entity\SessionInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class ObjectiveVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class ObjectiveVoter extends AbstractVoter
{
    /**
     * @var PermissionManagerInterface
     */
    protected $permissionManager;

    /**
     * @var ProgramYearStewardManagerInterface
     */
    protected $stewardManager;

    /**
     * @param PermissionManagerInterface $permissionManager
     * @param ProgramYearStewardManagerInterface $stewardManager
     */
    public function __construct(
        PermissionManagerInterface $permissionManager,
        ProgramYearStewardManagerInterface $stewardManager
    ) {
        $this->permissionManager = $permissionManager;
        $this->stewardManager = $stewardManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\ObjectiveInterface');
    }

    /**
     * @param string $attribute
     * @param ObjectiveInterface $objective
     * @param UserInterface|null $user
     * @return bool
     */
    protected function isGranted($attribute, $objective, $user = null)
    {
        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
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
     * @param UserInterface $user
     * @return bool
     */
    protected function isCreateEditDeleteGrantedForProgramYearObjective($objective, $user)
    {

        /* @var ProgramYearInterface $programYear */
        $programYear = $objective->getProgramYears()->first(); // there should ever only be one

        // Code below has been copy/pasted straight out of ProgramYearVoter::isGranted().
        // TODO: consolidate. [ST 2015/08/05]
        if ($programYear->isLocked() || $programYear->isArchived()) {
            return false;
        }
        return (
            (
                $this->userHasRole($user, ['Course Director', 'Developer'])
                && (
                    $this->schoolsAreIdentical($programYear->getSchool(), $user->getSchool())
                    || $this->permissionManager->userHasWritePermissionToSchool(
                        $user,
                        $programYear->getSchool()
                    )
                    || $this->stewardManager->schoolIsStewardingProgramYear($user, $programYear)
                )
            )
            || $this->permissionManager->userHasWritePermissionToProgram($user, $programYear->getProgram())
        );
    }

    /**
     * @param ObjectiveInterface $objective
     * @param UserInterface $user
     * @return bool
     */
    protected function isCreateEditDeleteGrantedForSessionObjective($objective, $user)
    {
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
            $this->userHasRole($user, ['Faculty', 'Course Director', 'Developer'])
            && (
                $this->schoolsAreIdentical($course->getSchool(), $user->getSchool())
                || $this->permissionManager->userHasWritePermissionToSchool($user, $course->getSchool())
            )
            || $this->permissionManager->userHasWritePermissionToCourse($user, $course)
        );
    }

    /**
     * @param ObjectiveInterface $objective
     * @param UserInterface $user
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
            $this->userHasRole($user, ['Faculty', 'Course Director', 'Developer'])
            && (
                $this->schoolsAreIdentical($course->getSchool(), $user->getSchool())
                || $this->permissionManager->userHasWritePermissionToSchool($user, $course->getSchool())
            )
            || $this->permissionManager->userHasWritePermissionToCourse($user, $course)
        );
    }
}
