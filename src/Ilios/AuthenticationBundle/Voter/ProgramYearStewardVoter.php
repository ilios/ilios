<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\Manager\PermissionManager;
use Ilios\CoreBundle\Entity\ProgramYearStewardInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class ProgramYearStewardVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class ProgramYearStewardVoter extends AbstractVoter
{
    /**
     * @var PermissionManager
     */
    protected $permissionManager;

    /**
     * @param PermissionManager $permissionManager
     */
    public function __construct(PermissionManager $permissionManager)
    {
        $this->permissionManager = $permissionManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof ProgramYearStewardInterface && in_array($attribute, array(
            self::VIEW, self::CREATE, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param ProgramYearStewardInterface $steward
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $steward, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                // the given user is granted VIEW permissions on the given steward
                // when at least one of the following statements is true
                // 1. The user's primary school is the same as the parent program's owning school
                //    and the user has at least one of 'Course Director', 'Faculty' and 'Developer' role.
                // 2. The user has READ permissions on the parent program's owning school
                //    and the user has at least one of 'Course Director', 'Faculty' and 'Developer' role.
                // 3. The user's primary school matches the stewarding school
                //    and the user has at least one of 'Course Director', 'Faculty' and 'Developer' role.
                // 4. The user has READ permissions on the owning program.
                return (
                    (
                        $this->userHasRole($user, ['Course Director', 'Developer', 'Faculty'])
                        && (
                            $this->schoolsAreIdentical(
                                $steward->getProgramOwningSchool(),
                                $user->getSchool()
                            )
                            || $this->permissionManager->userHasReadPermissionToSchool(
                                $user,
                                $steward->getProgramOwningSchool()->getId()
                            )
                            || $this->schoolsAreIdentical($steward->getSchool(), $user->getSchool())
                        )
                    )
                    || $this->permissionManager->userHasReadPermissionToProgram(
                        $user,
                        $steward->getProgram()
                    )
                );
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                // the given user is granted CREATE, EDIT and DELETE permissions on the given steward
                // when at least one of the following statements is true
                // 1. The user's primary school is the same as the parent program's owning school
                //    and the user has at least one of 'Course Director' and 'Developer' role.
                // 2. The user has WRITE permissions on the parent program's owning school
                //    and the user has at least one of 'Course Director' and 'Developer' role.
                // 3. The user's primary school matches the stewarding school
                //    and the user has at least one of 'Course Director' and 'Developer' role.
                // 4. The user has WRITE permissions on the parent program.
                return (
                    (
                        $this->userHasRole($user, ['Course Director', 'Developer'])
                        && (
                            $this->schoolsAreIdentical(
                                $steward->getProgramOwningSchool(),
                                $user->getSchool()
                            )
                            || $this->permissionManager->userHasWritePermissionToSchool(
                                $user,
                                $steward->getProgramOwningSchool()->getId()
                            )
                            || $this->schoolsAreIdentical($steward->getSchool(), $user->getSchool())
                        )
                    )
                    || $this->permissionManager->userHasWritePermissionToProgram(
                        $user,
                        $steward->getProgram()
                    )
                );
                break;
        }

        return false;
    }
}
