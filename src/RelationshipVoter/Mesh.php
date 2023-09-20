<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Classes\VoterPermissions;
use App\Entity\MeshConceptInterface;
use App\Entity\MeshDescriptorInterface;
use App\Entity\MeshPreviousIndexingInterface;
use App\Entity\MeshQualifierInterface;
use App\Entity\MeshTermInterface;
use App\Entity\MeshTreeInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class Mesh extends Voter
{
    public function supportsAttribute(string $attribute): bool
    {
        return $attribute === VoterPermissions::VIEW;
    }

    public function supportsType(string $subjectType): bool
    {
        return (
            is_a($subjectType, MeshConceptInterface::class, true)
            || is_a($subjectType, MeshDescriptorInterface::class, true)
            || is_a($subjectType, MeshPreviousIndexingInterface::class, true)
            || is_a($subjectType, MeshQualifierInterface::class, true)
            || is_a($subjectType, MeshTermInterface::class, true)
            || is_a($subjectType, MeshTreeInterface::class, true)
        );
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return (
            $this->supportsAttribute($attribute) && (
                $subject instanceof MeshConceptInterface ||
                $subject instanceof MeshDescriptorInterface ||
                $subject instanceof MeshPreviousIndexingInterface ||
                $subject instanceof MeshQualifierInterface ||
                $subject instanceof MeshTermInterface ||
                $subject instanceof MeshTreeInterface
            )
        );
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }
        if ($user->isRoot()) {
            return true;
        }

        if (
            $subject instanceof MeshConceptInterface ||
            $subject instanceof MeshDescriptorInterface ||
            $subject instanceof MeshPreviousIndexingInterface ||
            $subject instanceof MeshQualifierInterface ||
            $subject instanceof MeshTermInterface ||
            $subject instanceof MeshTreeInterface
        ) {
            return VoterPermissions::VIEW === $attribute;
        }

        return false;
    }
}
