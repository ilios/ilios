<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\Manager\ProgramYearStewardManagerInterface;
use Ilios\CoreBundle\Entity\Manager\PermissionManagerInterface;
use Ilios\CoreBundle\Entity\ProgramYearInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class ProgramYearVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class ProgramYearVoter extends AbstractVoter
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
        return array('Ilios\CoreBundle\Entity\ProgramYearInterface');
    }

    /**
     * @param string $attribute
     * @param ProgramYearInterface $programYear
     * @param UserInterface|null $user
     * @return bool
     */
    protected function isGranted($attribute, $programYear, $user = null)
    {
        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return $this->isViewGranted($programYear, $user);
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                return $this->isWriteGranted($programYear, $user);
                break;
        }

        return false;
    }

    /**
     * @param ProgramYearInterface $programYear
     * @param UserInterface $user
     * @return bool
     */
    protected function isViewGranted($programYear, $user)
    {
        // the given user is granted VIEW permissions on the given program year
        // when at least one of the following statements is true
        // 1. The user's primary school is the same as the parent program's owning school
        //    and the user has at least one of 'Course Director', 'Faculty' and 'Developer' role.
        // 2. The user has READ permissions on the parent program's owning school
        //    and the user has at least one of 'Course Director', 'Faculty' and 'Developer' role.
        // 3. The user's primary school matches at least one of the schools owning the
        //    program years' stewarding department
        //    and the user has at least one of 'Course Director', 'Faculty' and 'Developer' role.
        // 4. The user has READ permissions on the program.
        return (
            (
                $this->userHasRole($user, ['Course Director', 'Developer', 'Faculty'])
                && (
                    $this->schoolsAreIdentical($programYear->getSchool(), $user->getSchool())
                    || $this->permissionManager->userHasReadPermissionToSchool(
                        $user,
                        $programYear->getSchool()
                    )
                    || $this->stewardManager->schoolIsStewardingProgramYear($user, $programYear)
                )
            )
            || $this->permissionManager->userHasReadPermissionToProgram($user, $programYear->getProgram())
        );
    }

    /**
     * @param ProgramYearInterface $programYear
     * @param UserInterface $user
     * @return bool
     */
    protected function isWriteGranted($programYear, $user)
    {
        // the given user is granted CREATE/EDIT/DELETE permissions on the given program year
        // when at least one of the following statements is true
        // 1. The user's primary school is the same as the parent program's owning school
        //    and the user has at least one of 'Course Director' and 'Developer' role.
        // 2. The user has WRITE permissions on the parent program's owning school
        //    and the user has at least one of 'Course Director' and 'Developer' role.
        // 3. The user's primary school matches at least one of the schools owning the
        //    program years' stewarding department,
        //    and the user has at least one of 'Course Director' and 'Developer' role.
        // 4. The user has WRITE permissions on the parent program.
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
}
