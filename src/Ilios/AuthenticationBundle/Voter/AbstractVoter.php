<?php
namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\SchoolInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class AbstractVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
abstract class AbstractVoter extends Voter
{
    /**
     * @var string
     */
    const VIEW = 'view';

    /**
     * @var string
     */
    const EDIT = 'edit';

    /**
     * @var string
     */
    const DELETE = 'delete';

    /**
     * @var string
     */
    const CREATE = 'create';

    /**
     * Utility method, determines if a given user has any of the given roles.
     * @param UserInterface $user the user object
     * @param array $eligibleRoles a list of role names
     * @return bool TRUE if the user has at least one of the roles, FALSE otherwise.
     */
    public function userHasRole(UserInterface $user, array $eligibleRoles = array())
    {
        return $user->hasRole($eligibleRoles);
    }

    /**
     * Checks if two given schools are the same.
     * @param SchoolInterface|null $schoolA
     * @param SchoolInterface|null $schoolB
     * @return bool
     */
    public function schoolsAreIdentical(SchoolInterface $schoolA = null, SchoolInterface $schoolB = null)
    {
        return (
            $schoolA instanceof SchoolInterface
            && $schoolB instanceof SchoolInterface
            && $schoolA->getId() === $schoolB->getId()
        );
    }

    /**
     * Checks if two given users are the same.
     * @param UserInterface|null $userA
     * @param UserInterface|null $userB
     * @return bool
     */
    public function usersAreIdentical(UserInterface $userA = null, UserInterface $userB = null)
    {
        return (
            $userA instanceof UserInterface
            && $userB instanceof UserInterface
            && $userA->getId() === $userB->getId()
        );
    }
}
