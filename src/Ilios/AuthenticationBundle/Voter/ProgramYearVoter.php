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
                    ($this->userHasRole($user, ['Course Director', 'Developer', 'Faculty'])
                        && ($programYear->getProgram()->getOwningSchool()->getId()
                            === $user->getPrimarySchool()->getId()
                            || $this->permissionManager->userHasReadPermissionToSchool(
                                $user,
                                $programYear->getProgram()->getOwningSchool()
                            )
                            || $this->stewardManager->schoolIsStewardingProgramYear(
                                $user->getPrimarySchool(),
                                $programYear
                            )
                        )
                    )
                    || $this->permissionManager->userHasReadPermissionToProgram($user, $programYear->getProgram())
                );
                break;
            case self::EDIT:
            case self::DELETE:
                // the given user is granted EDIT and DELETE permissions on the given program year
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
                    ($this->userHasRole($user, ['Course Director', 'Developer'])
                        && ($programYear->getProgram()->getOwningSchool()->getId()
                            === $user->getPrimarySchool()->getId()
                            || $this->permissionManager->userHasWritePermissionToSchool(
                                $user,
                                $programYear->getProgram()->getOwningSchool()
                            )
                            || $this->stewardManager->schoolIsStewardingProgramYear(
                                $user->getPrimarySchool(),
                                $programYear
                            )
                        )
                    )
                    || $this->permissionManager->userHasWritePermissionToProgram($user, $programYear->getProgram())
                );
                break;
        }

        return false;
    }
}
