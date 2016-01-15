<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\Manager\PermissionManagerInterface;
use Ilios\CoreBundle\Entity\SchoolInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class SchoolVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class SchoolVoter extends AbstractVoter
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
        return $subject instanceof SchoolInterface && in_array($attribute, array(
            self::VIEW, self::CREATE, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param SchoolInterface $school
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $school, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                // Only grant VIEW permissions if the given school is the given user's
                // primary school
                // - or -
                // if the given user has been granted READ right on the given school
                // via the permissions system.
                return (
                    $this->schoolsAreIdentical($school, $user->getSchool())
                    || $this->userHasRole($user, ['Developer'])
                    || $this->permissionManager->userHasReadPermissionToSchool($user, $school)
                );
                break;
            case self::CREATE:
                // only developers can create schools.
                return $this->userHasRole($user, ['Developer']);
                break;
            case self::EDIT:
            case self::DELETE:
                // Only grant EDIT and DELETE permissions if the user has the 'Developer' role.
                // - and -
                // the user must be associated with the given school,
                // either by its primary school attribute
                //     - or - by WRITE rights for the school
                // via the permissions system.
                return (
                    $this->userHasRole($user, ['Developer'])
                    && (
                        $this->schoolsAreIdentical($school, $user->getSchool())
                        || $this->permissionManager->userHasWritePermissionToSchool($user, $school)
                    )
                );
                break;
        }

        return false;
    }
}
