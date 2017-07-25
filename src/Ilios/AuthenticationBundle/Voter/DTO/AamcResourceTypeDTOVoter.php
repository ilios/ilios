<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\CoreBundle\Entity\DTO\AamcResourceTypeDTO;
use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class AamcResourceTypeDTOVoter
 */
class AamcResourceTypeDTOVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof AamcResourceTypeDTO && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param AamcResourceTypeDTO $aamcResourceTypeDto
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $aamcResourceTypeDto, TokenInterface $token)
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
