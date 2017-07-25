<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\Manager\CourseManager;

/**
 * Class CourseVoter
 */
abstract class CourseVoter extends AbstractVoter
{
    /**
     * @var CourseManager
     */
    protected $courseManager;

    /**
     * @param CourseManager $courseManager
     */
    public function __construct(CourseManager $courseManager)
    {
        $this->courseManager = $courseManager;
    }

    /**
     * @param int $courseId
     * @param int $owningSchoolId
     * @param SessionUserInterface $user
     *
     * @return bool
     */
    protected function isViewGranted($courseId, $owningSchoolId, SessionUserInterface $user)
    {
        // grant VIEW privileges if at least one of the following
        // statements is true:
        // 1. the user's primary school is the course's owning school
        // 2. the user is instructing ILMs or offerings in this course
        // 3. the user is directing this course
        // 4. the user has READ rights on the course's owning school via the permissions system
        // 5. the user has READ rights on the course via the permissions system
        return (
            $owningSchoolId === $user->getSchoolId()
            || $this->courseManager->isUserInstructingInCourse($user->getId(), $courseId)
            || $user->isDirectingCourse($courseId)
            || $user->hasReadPermissionToSchool($owningSchoolId)
            || $user->hasReadPermissionToCourse($courseId)
        );
    }

    /**
     * @param int $courseId
     * @param int $owningSchoolId
     * @param SessionUserInterface $user
     *
     * @return bool
     */
    protected function isWriteGranted($courseId, $owningSchoolId, SessionUserInterface $user)
    {
        // grant CREATE/EDIT/DELETE privileges if at least one of the following
        // statements is true:
        // 1. the user's primary school is the course's owning school
        //    and the user has at least one of the 'Faculty', 'Course Director' and 'Developer' roles.
        // 2. the user has WRITE rights on the course's owning school via the permissions system
        //    and the user has at least one of the 'Faculty', 'Course Director' and 'Developer' roles.
        // 3. the user has WRITE rights on the course via the permissions system
        return (
            $user->hasRole(['Faculty', 'Course Director', 'Developer'])
            && (
                $owningSchoolId === $user->getSchoolId()
                || $user->hasWritePermissionToSchool($owningSchoolId)
            )
            || $user->hasWritePermissionToCourse($courseId)
        );
    }
}
