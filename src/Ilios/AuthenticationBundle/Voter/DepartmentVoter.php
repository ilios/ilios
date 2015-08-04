<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\DepartmentInterface;
use Ilios\CoreBundle\Entity\Manager\PermissionManagerInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class DepartmentVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class DepartmentVoter extends AbstractVoter
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
        return array('Ilios\CoreBundle\Entity\DepartmentInterface');
    }

    /**
     * @param string $attribute
     * @param DepartmentInterface $department
     * @param UserInterface $user
     * @return bool
     */
    protected function isGranted($attribute, $department, $user = null)
    {
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            // grant VIEW privileges
            // if the user's primary school is the the departments's owning school
            // - or -
            // if the user has READ rights on the department's owning school
            // via the permissions system.
            case self::VIEW:
                return ($department->getSchool()->getId() === $user->getPrimarySchool()
                    || $this->permissionManager->userHasReadPermissionToSchool($user, $department->getSchool())
                );
                break;
            case self::EDIT:
            case self::DELETE:
                // grant EDIT and DELETE privileges
                // if the user has the 'Developer' role
                // - and -
                //   if the user's primary school is the the department's owning school
                //   - or -
                //   if the user has WRITE rights on the departments's owning school
                // via the permissions system.
                return ($this->userHasRole($user, 'Developer')
                    && ($department->getSchool()->getId() === $user->getPrimarySchool()
                        || $this->permissionManager->userHasWritePermissionToSchool($user, $department->getSchool())
                    )
                );
                break;
        }

        return false;
    }
}
