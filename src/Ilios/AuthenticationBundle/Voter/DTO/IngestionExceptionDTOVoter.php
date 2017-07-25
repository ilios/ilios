<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\CoreBundle\Entity\DTO\IngestionExceptionDTO;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class IngestionExceptionDTOVoter
 */
class IngestionExceptionDTOVoter extends AbstractVoter
{
    /**
     * @inheritdoc
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof IngestionExceptionDTO && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param IngestionExceptionDTO $ingestionException
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $ingestionException, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                // Grant VIEW access only to users with the Developer role.
                return $user->hasRole(['Developer']);
                break;
        }

        return false;
    }
}
