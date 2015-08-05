<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\CompetencyInterface;
use Ilios\CoreBundle\Entity\Manager\PermissionManagerInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class CompetencyVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class CompetencyVoter extends AbstractVoter
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
        return array('Ilios\CoreBundle\Entity\CompetencyInterface');
    }

    /**
     * @param string $attribute
     * @param CompetencyInterface $competency
     * @param UserInterface $user
     * @return bool
     */
    protected function isGranted($attribute, $competency, $user = null)
    {
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            // grant VIEW privileges
            // if the user's primary school is the the competency's owning school
            // - or -
            // if the user has READ rights on the competency's owning school
            // via the permissions system.
            case self::VIEW:
                return ($competency->getSchool()->getId() === $user->getPrimarySchool()
                    || $this->permissionManager->userHasReadPermissionToSchool($user, $competency->getSchool())
                );
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                // grant CREATE, EDIT and DELETE privileges
                // if the user has the 'Developer' role
                // - and -
                //   if the user's primary school is the the competency's owning school
                //   - or -
                //   if the user has WRITE rights on the competency's owning school
                // via the permissions system.
                return ($this->userHasRole($user, 'Developer')
                    && ($competency->getSchool()->getId() === $user->getPrimarySchool()
                        || $this->permissionManager->userHasWritePermissionToSchool($user, $competency->getSchool())
                    )
                );
                break;
        }

        return false;
    }
}
