<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\AuthenticationBundle\Voter\Entity\MeshEntityVoter;
use Ilios\CoreBundle\Entity\DTO\MeshDescriptorDTO;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class MeshDescriptorDTOVoter
 * @package Ilios\AuthenticationBundle\Voter\DTO
 */
class MeshDescriptorDTOVoter extends MeshEntityVoter
{
    /**
     * @inheritdoc
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof MeshDescriptorDTO && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param MeshDescriptorDTO $requestedDescriptor
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $requestedDescriptor, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
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
