<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\Manager\PermissionManagerInterface;
use Ilios\CoreBundle\Entity\ProgramInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class ProgramVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class ProgramVoter extends AbstractVoter
{
    /**
     * @var PermissionManagerInterface
     */
    protected $permissionManager;

    /**
     * @param PermissionManagerInterface $permissionManager
     */
    public function __construct(PermissionManagerInterface $permissionManager)
    {
        $this->permissionManager = $permissionManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\ProgramInterface');
    }

    /**
     * @param string $attribute
     * @param ProgramInterface $program
     * @param UserInterface|null $user
     * @return bool
     */
    protected function isGranted($attribute, $program, $user = null)
    {
        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                // the given user is granted VIEW permissions on the given program
                // when at least one of the following statements is true
                // 1. The user's primary school is the same as the program's owning school
                //    and the user has at least one of 'Course Director', 'Faculty' and 'Developer' role.
                // 2. The user has READ permissions on the program's owning school
                //    and the user has at least one of 'Course Director', 'Faculty' and 'Developer' role.
                // 3. The user has READ permissions on the program.
                return (
                    (
                        $this->userHasRole($user, ['Course Director', 'Developer', 'Faculty'])
                        && (
                            $this->schoolsAreIdentical($program->getSchool(), $user->getSchool())
                            || $this->permissionManager->userHasReadPermissionToSchool(
                                $user,
                                $program->getSchool()
                            )
                        )
                    )
                    || $this->permissionManager->userHasReadPermissionToProgram($user, $program)
                );
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                // the given user is grantedC CREATE, EDIT and DELETE permissions on the given program
                // when at least one of the following statements is true
                // 1. The user's primary school is the same as the program's owning school
                //    and the user has at least one of 'Course Director' and 'Developer' role.
                // 2. The user has WRITE permissions on the program's owning school
                //    and the user has at least one of 'Course Director' and 'Developer' role.
                // 3. The user has WRITE permissions on the program.
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
                break;
        }

        return false;
    }
}
