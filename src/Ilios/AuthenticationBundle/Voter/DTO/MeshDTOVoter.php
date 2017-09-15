<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\AuthenticationBundle\Voter\Entity\MeshEntityVoter;
use Ilios\CoreBundle\Entity\DTO\MeshConceptDTO;
use Ilios\CoreBundle\Entity\DTO\MeshDescriptorDTO;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\DTO\MeshPreviousIndexingDTO;
use Ilios\CoreBundle\Entity\DTO\MeshQualifierDTO;
use Ilios\CoreBundle\Entity\DTO\MeshTermDTO;
use Ilios\CoreBundle\Entity\DTO\MeshTreeDTO;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class MeshDescriptorDTOVoter
 */
class MeshDTOVoter extends MeshEntityVoter
{
    /**
     * @inheritdoc
     */
    protected function supports($attribute, $subject)
    {
        return (
                $subject instanceof MeshDescriptorDTO ||
                $subject instanceof MeshConceptDTO ||
                $subject instanceof MeshTermDTO ||
                $subject instanceof MeshPreviousIndexingDTO ||
                $subject instanceof MeshQualifierDTO ||
                $subject instanceof MeshTreeDTO
            ) && in_array($attribute, [self::VIEW]);
    }

    /**
     * @param string $attribute
     * @param $requestedDescriptor
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $requestedDescriptor, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return true;
                break;
        }

        return false;
    }
}
