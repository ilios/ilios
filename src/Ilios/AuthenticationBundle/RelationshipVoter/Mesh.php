<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\ApplicationConfigInterface;
use Ilios\CoreBundle\Entity\DTO\ApplicationConfigDTO;
use Ilios\CoreBundle\Entity\DTO\MeshConceptDTO;
use Ilios\CoreBundle\Entity\DTO\MeshDescriptorDTO;
use Ilios\CoreBundle\Entity\DTO\MeshPreviousIndexingDTO;
use Ilios\CoreBundle\Entity\DTO\MeshQualifierDTO;
use Ilios\CoreBundle\Entity\DTO\MeshTermDTO;
use Ilios\CoreBundle\Entity\DTO\MeshTreeDTO;
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
            (
                (
                    $subject instanceof MeshConceptDTO ||
                    $subject instanceof MeshDescriptorDTO ||
                    $subject instanceof MeshPreviousIndexingDTO ||
                    $subject instanceof MeshQualifierDTO ||
                    $subject instanceof MeshTermDTO ||
                    $subject instanceof MeshTreeDTO
                ) && in_array($attribute, [self::VIEW])
            )
            or
            (
                (
                    $subject instanceof MeshConceptInterface ||
                    $subject instanceof MeshDescriptorInterface ||
                    $subject instanceof MeshPreviousIndexingInterface ||
                    $subject instanceof MeshQualifierInterface ||
                    $subject instanceof MeshTermInterface ||
                    $subject instanceof MeshTreeInterface
                ) && in_array($attribute, [self::CREATE, self::VIEW, self::EDIT, self::DELETE])
            )
        );
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

        if ($subject instanceof MeshConceptDTO ||
            $subject instanceof MeshDescriptorDTO ||
            $subject instanceof MeshPreviousIndexingDTO ||
            $subject instanceof MeshQualifierDTO ||
            $subject instanceof MeshTermDTO ||
            $subject instanceof MeshTreeDTO
        ) {
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
