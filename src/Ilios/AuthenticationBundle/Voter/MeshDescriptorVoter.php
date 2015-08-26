<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\MeshdescriptorInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class MeshDescriptorVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class MeshDescriptorVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\MeshDescriptorInterface');
    }

    /**
     * @param string $attribute
     * @param MeshDescriptorInterface $meshDescriptor
     * @param UserInterface $user
     * @return bool
     */
    protected function isGranted($attribute, $meshDescriptor, $user = null)
    {
        if (!$user instanceof UserInterface) {
            return false;
        }

        // all authenticated users can view Mesh Descriptors,
        // but only developers can create/modify/delete them directly.
        switch ($attribute) {
            case self::VIEW:
                return true;
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                return $this->userHasRole($user, ['Developer']);
                break;
        }

        return false;
    }
}
