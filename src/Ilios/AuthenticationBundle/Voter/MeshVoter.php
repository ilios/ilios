<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\MeshConceptInterface;
use Ilios\CoreBundle\Entity\MeshDescriptorInterface;
use Ilios\CoreBundle\Entity\MeshPreviousIndexingInterface;
use Ilios\CoreBundle\Entity\MeshQualifierInterface;
use Ilios\CoreBundle\Entity\MeshSemanticTypeInterface;
use Ilios\CoreBundle\Entity\MeshTermInterface;
use Ilios\CoreBundle\Entity\MeshTreeInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class MeshVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class MeshVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return (
            $subject instanceof MeshConceptInterface ||
            $subject instanceof MeshDescriptorInterface ||
            $subject instanceof MeshPreviousIndexingInterface ||
            $subject instanceof MeshQualifierInterface ||
            $subject instanceof MeshSemanticTypeInterface ||
            $subject instanceof MeshTermInterface ||
            $subject instanceof MeshTreeInterface
        ) && in_array($attribute, array(self::CREATE, self::VIEW, self::EDIT, self::DELETE));
    }

    /**
     * @param string $attribute
     * @param object $meshObject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $meshObject, TokenInterface $token)
    {
        $user = $token->getUser();
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
