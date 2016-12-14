<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\CoreBundle\Entity\DTO\AlertChangeTypeDTO;
use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class AlertChangeTypeDTOVoter
 * @package Ilios\AuthenticationBundle\Voter\DTO
 */
class AlertChangeTypeDTOVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof AlertChangeTypeDTO && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param AlertChangeTypeDTO $alertChangeTypeDto
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $alertChangeTypeDto, TokenInterface $token)
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
