<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\SessionTypeInterface;
use Ilios\CoreBundle\Entity\Manager\PermissionManagerInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class SessionTypeVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class SessionTypeVoter extends AbstractVoter
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
        return array('Ilios\CoreBundle\Entity\SessionTypeInterface');
    }

    /**
     * @param string $attribute
     * @param SessionTypeInterface $sessionType
     * @param UserInterface $user
     * @return bool
     */
    protected function isGranted($attribute, $sessionType, $user = null)
    {
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            // grant VIEW privileges
            // if the user's primary school is the session type's owning school
            // - or -
            // if the user has READ rights on the session type's owning school
            // via the permissions system.
            case self::VIEW:
                return ($sessionType->getOwningSchool()->getId() === $user->getPrimarySchool()
                    || $this->permissionManager->userHasReadPermissionToSchool($user, $sessionType->getOwningSchool())
                );
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                // grant CREATE, EDIT and DELETE privileges
                // if the user has the 'Developer' role
                // - and -
                //   if the user's primary school is the session type's owning school
                //   - or -
                //   if the user has WRITE rights on the session type's owning school
                // via the permissions system.
                return ($this->userHasRole($user, 'Developer')
                    && ($sessionType->getOwningSchool()->getId() === $user->getPrimarySchool()
                        || $this->permissionManager->userHasWritePermissionToSchool(
                            $user,
                            $sessionType->getOwningSchool()
                        )
                    )
                );
                break;
        }

        return false;
    }
}
