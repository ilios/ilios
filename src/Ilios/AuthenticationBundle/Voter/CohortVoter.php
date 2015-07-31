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
    protected $stewardHandler;

    /**
     * @param PermissionManagerInterface $permissionManager
     * @param ProgramYearStewardManagerInterface $stewardManager
     */
    public function __construct(PermissionManagerInterface $permissionManager, ProgramYearStewardManagerInterface $stewardManager)
    {
        $this->permissionHandler = $permissionManager;
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
                return $this->hasViewPermissions($user, $cohort);
                break;
            case self::EDIT:
                return $this->hasEditPermissions($user, $cohort);
                break;
            case self::DELETE:
                // can't do that at the moment
                return false;
                break;
        }

        return false;
    }

    /**
     * @param UserInterface $user
     * @param CohortInterface $cohort
     * @return bool
     */
    protected function hasViewPermissions(UserInterface $user, CohortInterface $cohort)
    {
        //
        // View permissions to the given cohort by the given user are granted if at least one of the following
        // statements are true:
        //
        // 1. The user's primary school is the same as the cohort's owning school,
        //    and the user holds at least one of 'Developer', 'Course Director' or 'Faculty' roles.
        // 2. The user's primary school is the same as an owning school of at least one stewarding department,
        //    and the user holds at least one of 'Developer', 'Course Director' or 'Faculty' roles.
        // 3. The user has been granted 'read' rights to the cohort's owning program via the 'permissions' system.
        //    and the user holds at least one of 'Developer', 'Course Director' or 'Faculty' roles.
        // 4. The user has been granted 'read' rights to the cohort's owning school via the 'permissions' system.
        //
        $cohortOwningSchoolId = $cohort->getProgramYear()->getProgram()->getOwningSchool()->getId();
        $cohortOwningProgramId = $cohort->getProgramYear()->getProgram()->getId();
        $userPrimarySchoolId = $user->getPrimarySchool()->getId();
        $hasRole = $this->userHasRole($user, ['Cohort Director', 'Developer', 'Faculty']);
        $permissionGranted = false;

        // 1.
        if ($cohortOwningSchoolId === $userPrimarySchoolId && $hasRole) {
            $permissionGranted =  true;
        }
        if ($permissionGranted) {
            return true;
        }

        // 2.
        $departments = $this->stewardHandler->findProgramYearStewardsBy([
            'programYear' => $cohort->getProgramYear()->getId(),
        ]);
        foreach ($departments as $department) {
            if ($department->getSchool() === $userPrimarySchoolId && $hasRole) {
                $permissionGranted = true;
                break;
            }
        }
        if ($permissionGranted) {
            return true;
        }

        // 3.
        $schoolPermissions = $this->permissionHandler->findPermissionsBy([
            'user' => $user->getId(),
            'tableName' => 'school',
            'canRead' => true,
        ]);
        foreach ($schoolPermissions as $permission) {
            if ($permission->getTableRowId() === $cohortOwningSchoolId && $hasRole) {
                $permissionGranted = true;
                break;
            }
        }
        if ($permissionGranted) {
            return true;
        }

        // 4.
        $programPermissions =  $this->permissionHandler->findPermissionsBy([
            'user' => $user->getId(),
            'tableName' => 'program',
            'canRead' => true,
        ]);
        foreach ($programPermissions as $permission) {
            if ($cohortOwningProgramId === $permission->getTableRowId()) {
                $permissionGranted = true;
                break;
            }
        }

        return $permissionGranted;
    }

    protected function hasEditPermissions(UserInterface $user, CohortInterface $cohort)
    {
        //
        // View permissions to the given cohort by the given user are granted if at least one of the following
        // statements are true:
        //
        // 1. The user's primary school is the same as the cohort's owning school,
        //    and the user holds 'Developer' and/or 'Course Director' roles.
        // 2. The user's primary school is the same as an owning school of at least one stewarding department,
        //    and the user holds 'Developer' and/or 'Course Director' roles.
        // 3. The user has been granted 'write' rights to the cohort's owning program via the 'permissions' system.
        //    and the user holds 'Developer' and/or 'Course Director' roles.
        // 4. The user has been granted 'write' rights to the cohort's owning school via the 'permissions' system.
        //
        $cohortOwningSchoolId = $cohort->getProgramYear()->getProgram()->getOwningSchool()->getId();
        $cohortOwningProgramId = $cohort->getProgramYear()->getProgram()->getId();
        $userPrimarySchoolId = $user->getPrimarySchool()->getId();
        $hasRole = $this->userHasRole($user, ['Cohort Director', 'Developer']);
        $permissionGranted = false;

        // 1.
        if ($cohortOwningSchoolId === $userPrimarySchoolId && $hasRole) {
            $permissionGranted =  true;
        }
        if ($permissionGranted) {
            return true;
        }

        // 2.
        $departments = $this->stewardHandler->findProgramYearStewardsBy([
            'programYear' => $cohort->getProgramYear()->getId(),
        ]);
        foreach ($departments as $department) {
            if ($department->getSchool() === $userPrimarySchoolId && $hasRole) {
                $permissionGranted = true;
                break;
            }
        }
        if ($permissionGranted) {
            return true;
        }

        // 3.
        $schoolPermissions = $this->permissionHandler->findPermissionsBy([
            'user' => $user->getId(),
            'tableName' => 'school',
            'canWrite' => true,
        ]);
        foreach ($schoolPermissions as $permission) {
            if ($permission->getTableRowId() === $cohortOwningSchoolId && $hasRole) {
                $permissionGranted = true;
                break;
            }
        }
        if ($permissionGranted) {
            return true;
        }

        // 4.
        $programPermissions =  $this->permissionHandler->findPermissionsBy([
            'user' => $user->getId(),
            'tableName' => 'program',
            'canWrite' => true,
        ]);
        foreach ($programPermissions as $permission) {
            if ($cohortOwningProgramId === $permission->getTableRowId()) {
                $permissionGranted = true;
                break;
            }
        }

        return $permissionGranted;
    }

}
