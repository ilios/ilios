<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\Manager\PermissionManagerInterface;
use Ilios\CoreBundle\Entity\ProgramInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

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
    protected function supports($attribute, $subject)
    {
        return $subject instanceof ProgramInterface && in_array($attribute, array(
            self::VIEW, self::CREATE, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param ProgramInterface $program
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $program, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                // do not enforce special views permissions on programs.
                return true;
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
                                $program->getSchool()->getId()
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
