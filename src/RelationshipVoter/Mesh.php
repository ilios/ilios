<?php

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Entity\MeshConceptInterface;
use App\Entity\MeshDescriptorInterface;
use App\Entity\MeshPreviousIndexingInterface;
use App\Entity\MeshQualifierInterface;
use App\Entity\MeshTermInterface;
use App\Entity\MeshTreeInterface;
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
