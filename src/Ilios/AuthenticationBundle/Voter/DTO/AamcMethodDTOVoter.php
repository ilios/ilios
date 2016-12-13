<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\CoreBundle\Entity\DTO\AamcMethodDTO;
use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class AamcMethodDTOVoter
 * @package Ilios\AuthenticationBundle\Voter\DTO
 */
class AamcMethodDTOVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof AamcMethodDTO && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param AamcMethodDTO $aamcMethodDto
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $aamcMethodDto, TokenInterface $token)
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
