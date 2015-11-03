<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\CourseInterface;
use Ilios\CoreBundle\Entity\Manager\CourseManagerInterface;
use Ilios\CoreBundle\Entity\Manager\PermissionManagerInterface;
use Ilios\CoreBundle\Entity\Manager\ProgramManagerInterface;
use Ilios\CoreBundle\Entity\Manager\ProgramYearManagerInterface;
use Ilios\CoreBundle\Entity\Manager\ProgramYearStewardManagerInterface;
use Ilios\CoreBundle\Entity\Manager\SessionManagerInterface;
use Ilios\CoreBundle\Entity\PublishEventInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class PublishEventVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class PublishEventVoter extends AbstractVoter
{
    /**
     * @var PermissionManagerInterface
     */
    protected $permissionManager;

    /**
     * @var ProgramManagerInterface
     */
    protected $programManager;

    /**
     * @var ProgramYearManagerInterface
     */
    protected $programYearManager;

    /**
     * @var CourseManagerInterface
     */
    protected $courseManager;

    /**
     * @var SessionManagerInterface
     */
    protected $sessionManager;

    /**
     * @var ProgramYearStewardManagerInterface
     */
    protected $stewardManager;

    /**
     * @param PermissionManagerInterface $permissionManager
     * @param ProgramManagerInterface $programManager
     * @param ProgramYearManagerInterface $programYearManager
     * @param CourseManagerInterface $courseManager
     * @param SessionManagerInterface $sessionManager
     * @param ProgramYearStewardManagerInterface $stewardManager
     */
    public function __construct(
        PermissionManagerInterface $permissionManager,
        ProgramManagerInterface $programManager,
        ProgramYearManagerInterface $programYearManager,
        CourseManagerInterface $courseManager,
        SessionManagerInterface $sessionManager,
        ProgramYearStewardManagerInterface $stewardManager
    ) {
        $this->permissionManager = $permissionManager;
        $this->programManager = $programManager;
        $this->programYearManager = $programYearManager;
        $this->courseManager = $courseManager;
        $this->sessionManager = $sessionManager;
        $this->stewardManager = $stewardManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSupportedAttributes()
    {
        return array(self::CREATE, self::VIEW);
    }

    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\PublishEventInterface');
    }

    /**
     * @param string $attribute
     * @param PublishEventInterface $event
     * @param UserInterface|null $user
     * @return bool
     */
    protected function isGranted($attribute, $event, $user = null)
    {
        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                // For the sake of keeping it fast and simple,
                // any authenticated user can see all publish events.
                return true;
                break;
            case self::CREATE:
                // let any user with faculty/course director/developer roles create new publish events.
                return $this->userHasRole($user, ['Faculty', 'Course Director', 'Developer']);
                break;
        }

        return false;
    }

    /**
     * @param PublishEventInterface $event
     * @param UserInterface $user
     * @return bool
     * @see ProgramVoter::isGranted()
     */
    protected function isCreateGrantedForProgramPublishEvent($event, $user)
    {
        $program = $this->programManager->findProgramBy(['id' => $event->getTableRowId()]);
        if (empty($program)) {
            return false; // ¯\_(ツ)_/¯
        }

        // copied and pasted straight out of ProgramVoter::isGranted().
        // TODO: consolidate [ST 2015/08/05]
        return (
            (
                $this->userHasRole($user, ['Course Director', 'Developer'])
                && (
                    $this->schoolsAreIdentical($program->getSchool(), $user->getSchool())
                    || $this->permissionManager->userHasWritePermissionToSchool(
                        $user,
                        $program->getSchool()
                    )
                )
            )
            || $this->permissionManager->userHasWritePermissionToProgram($user, $program)
        );
    }

    /**
     * @param PublishEventInterface $event
     * @param UserInterface $user
     * @return bool
     *
     * @see ProgramYearVoter::isGranted()
     */
    protected function isCreateGrantedForProgramYearPublishEvent($event, $user)
    {
        $programYear = $this->programYearManager->findProgramYearBy(['id' => $event->getTableRowId()]);

        if (empty($programYear)) {
            return false;
        }

        // copied and pasted straight out of ProgramYearVoter::isGranted().
        // TODO: consolidate [ST 2015/08/05]
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
     * @param PublishEventInterface $event
     * @param UserInterface $user
     * @return bool
     *
     * @see CourseVoter::isGranted()
     */
    protected function isCreateGrantedForCoursePublishEvent($event, $user)
    {
        $course = $this->courseManager->findCourseBy(['id' => $event->getTableRowId()]);

        if (empty($course)) {
            return false;
        }

        return $this->isCreateGrantedForCourse($course, $user);
    }

    /**
     * @param PublishEventInterface $event
     * @param UserInterface $user
     * @return bool
     *
     * @see CourseVoter::isGranted()
     */
    protected function isCreateGrantedForSessionPublishEvent($event, $user)
    {
        $session = $this->sessionManager->findSessionBy(['id' => $event->getTableRowId()]);

        if (empty($session) || ! $session->getCourse()) {
            return false;
        }

        return $this->isCreateGrantedForCourse($session->getCourse(), $user);
    }

    /**
     * @param \Ilios\CoreBundle\Entity\CourseInterface $course
     * @param \Ilios\CoreBundle\Entity\UserInterface $user
     * @return bool
     */
    private function isCreateGrantedForCourse(CourseInterface $course, UserInterface $user)
    {
        // copied and pasted from CourseManager::isGranted()
        // TODO: consolidate [ST 2015/08/05]
        // HALT!
        // deny CREATE privileges if the course is locked or archived.
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
