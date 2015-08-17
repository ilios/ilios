<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\SchoolInterface;
use Ilios\CoreBundle\Entity\UserRoleInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\CoreBundle\Traits\SchoolEntity;
use Ilios\CoreBundle\Traits\SchoolEntityInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter as Voter;

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
     * {@inheritdoc}
     */
    protected function getSupportedAttributes()
    {
        return array(self::CREATE, self::VIEW, self::EDIT, self::DELETE);
    }

    /**
     * Utility method, determines if a given user has any of the given roles.
     * @param UserInterface $user the user object
     * @param array $eligibleRoles a list of role names
     * @return bool TRUE if the user has at least one of the roles, FALSE otherwise.
     */
    public function userHasRole(UserInterface $user, $eligibleRoles = array())
    {
        $roles = array_map(
            function (UserRoleInterface $role) {
                return $role->getTitle();
            },
            $user->getRoles()->toArray()
        );

        $intersection = array_intersect($eligibleRoles, $roles);

        return ! empty($intersection);
    }

    /**
     * Checks if the schools of two given entities are the same.
     * @param SchoolEntityInterface $entityA
     * @param SchoolEntityInterface $entityB
     * @return bool
     */
    public function schoolsAreIdentical(SchoolEntityInterface $entityA, SchoolEntityInterface $entityB)
    {
        $schoolA = $entityA->getSchool();
        $schoolB = $entityB->getSchool();

        return (
            $schoolA instanceof SchoolInterface
            && $schoolB instanceof SchoolInterface
            && $schoolA->getId() === $schoolB->getId()
        );
    }
}
