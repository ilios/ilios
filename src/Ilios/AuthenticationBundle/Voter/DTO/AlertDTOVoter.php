<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\CoreBundle\Entity\DTO\AlertDTO;
use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class AlertDTOVoter
 * @package Ilios\AuthenticationBundle\Voter\DTO
 */
class AlertDTOVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof AlertDTO && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param AlertDTO $alertDto
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $alertDto, TokenInterface $token)
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
