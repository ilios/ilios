<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\LearningMaterialInterface;
use Ilios\CoreBundle\Entity\Manager\PermissionManagerInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class LearningMaterialVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class LearningMaterialVoter extends AbstractVoter
{
    /**
     * @var PermissionManagerInterface
     */
    protected $permissionManager;

    public function __construct(PermissionManagerInterface $permissionManager)
    {
        $this->permissionManager = $permissionManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\LearningMaterialInterface');
    }

    /**
     * @param string $attribute
     * @param LearningMaterialInterface $material
     * @param UserInterface|null $user
     * @return bool
     */
    protected function isGranted($attribute, $material, $user = null)
    {
        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                // any authenticated user can see all learning materials.
                return true;
                break;
            case self::CREATE:
                // users with 'Faculty', 'Course director' or 'Developer' role can create materials.
                return $this->userHasRole($user, ['Faculty', 'Course Director', 'Developer']);
                break;
            case self::EDIT:
            case self::DELETE:
                // in order to grant EDIT and DELETE privileges on the given learning material to the given user,
                // at least one of the following statements must be true:
                // 1. the user owns the learning material
                // 2. the user has at least one of 'Faculty', 'Course Director' or 'Developer' roles.
                return (
                    $this->usersAreIdentical($user, $material->getOwningUser())
                    || $this->userHasRole($user, ['Faculty', 'Course Director', 'Developer'])
                );
                break;
        }

        return false;
    }
}
