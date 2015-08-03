<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\CohortInterface;
use Ilios\CoreBundle\Entity\Manager\ProgramYearStewardManagerInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\CoreBundle\Entity\Manager\PermissionManagerInterface;

/**
 * Class CohortVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class CohortVoter extends AbstractVoter
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
        $this->permissionHandler = $permissionManager;
        $this->stewardManager = $stewardManager;
    }
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\CohortInterface');
    }

    /**
     * @param string $attribute
     * @param CohortInterface $cohort
     * @param UserInterface|null $user
     * @return bool
     */
    protected function isGranted($attribute, $cohort, $user = null)
    {
        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                // the given user is granted VIEW permissions on the given cohort
                // when at least one of the following statements is true
                // 1. The user's primary school is the same as the parent program's owning school
                //    and the user has at least one of 'Course Director', 'Faculty' and 'Developer' role.
                // 2. The user has READ permissions on the parent program's owning school
                //    and the user has at least one of 'Course Director', 'Faculty' and 'Developer' role.
                // 3. The user's primary school matches at least one of the schools owning the associated
                //    program years' stewarding department
                //    and the user has at least one of 'Course Director', 'Faculty' and 'Developer' role.
                // 4. The user has READ permissions on the program.
                return (
                    ($this->userHasRole($user, ['Course Director', 'Developer', 'Faculty'])
                        && ($cohort->getProgramYear()->getProgram()->getOwningSchool()->getId() === $user->getPrimarySchool()->getId()
                            || $this->permissionManager->userHasReadPermissionToSchool($user, $cohort->getProgramYear()->getProgram()->getOwningSchool()))
                        || $this->stewardManager->schoolIsStewardingProgramYear($user->getPrimarySchool(), $cohort->getProgramYear()))
                    || $this->permissionManager->userHasReadPermissionToProgram($user, $cohort->getProgramYear()->getProgram())
                );
                break;
            case self::EDIT:
            case self::DELETE:
                // the given user is granted EDIT and DELETE permissions on the given cohort
                // when at least one of the following statements is true
                // 1. The user's primary school is the same as the parent program's owning school
                //    and the user has at least one of 'Course Director' and 'Developer' role.
                // 2. The user has WRITE permissions on the parent program's owning school
                //    and the user has at least one of 'Course Director' and 'Developer' role.
                // 3. The user's primary school matches at least one of the schools owning the associated
                //    program years' stewarding department,
                //    and the user has at least one of 'Course Director' and 'Developer' role.
                // 4. The user has WRITE permissions on the parent program.
                return (
                    ($this->userHasRole($user, ['Course Director', 'Developer'])
                        && ($cohort->getProgramYear()->getProgram()->getOwningSchool()->getId() === $user->getPrimarySchool()->getId()
                            || $this->permissionManager->userHasWritePermissionToSchool($user, $cohort->getProgramYear()->getProgram()->getOwningSchool()))
                        || $this->stewardManager->schoolIsStewardingProgramYear($user->getPrimarySchool(), $cohort->getProgramYear()))
                    || $this->permissionManager->userHasWritePermissionToProgram($user, $cohort->getProgramYear()->getProgram())
                );
                break;
        }

        return false;
    }
}
