<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class MeshVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class MeshVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return [
            'Ilios\CoreBundle\Entity\MeshConceptInterface',
            'Ilios\CoreBundle\Entity\MeshDescriptorInterface',
            'Ilios\CoreBundle\Entity\MeshPreviousIndexingInterface',
            'Ilios\CoreBundle\Entity\MeshQualifierInterface',
            'Ilios\CoreBundle\Entity\MeshSemanticTypeInterface',
            'Ilios\CoreBundle\Entity\MeshTermInterface',
        ];
    }

    /**
     * @param string $attribute
     * @param Object $meshDescriptor
     * @param UserInterface $user
     * @return bool
     */
    protected function isGranted($attribute, $meshObject, $user = null)
    {
        if (!$user instanceof UserInterface) {
            return false;
        }

        // all authenticated users can view Mesh Stuff,
        // but only developers can create/modify/delete them.
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
