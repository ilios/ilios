<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\MeshConceptInterface;
use Ilios\CoreBundle\Entity\MeshDescriptorInterface;
use Ilios\CoreBundle\Entity\MeshPreviousIndexingInterface;
use Ilios\CoreBundle\Entity\MeshQualifierInterface;
use Ilios\CoreBundle\Entity\MeshTermInterface;
use Ilios\CoreBundle\Entity\MeshTreeInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class Mesh extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return (
                $subject instanceof MeshConceptInterface ||
                $subject instanceof MeshDescriptorInterface ||
                $subject instanceof MeshPreviousIndexingInterface ||
                $subject instanceof MeshQualifierInterface ||
                $subject instanceof MeshTermInterface ||
                $subject instanceof MeshTreeInterface
            ) && (self::VIEW === $attribute);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }
        if ($user->isRoot()) {
            return true;
        }

        if ($subject instanceof MeshConceptInterface ||
            $subject instanceof MeshDescriptorInterface ||
            $subject instanceof MeshPreviousIndexingInterface ||
            $subject instanceof MeshQualifierInterface ||
            $subject instanceof MeshTermInterface ||
            $subject instanceof MeshTreeInterface
        ) {
            return self::VIEW === $attribute;
        }

        return false;
    }
}
