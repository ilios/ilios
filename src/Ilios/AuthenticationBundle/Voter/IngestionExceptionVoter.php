<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\IngestionException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class SchoolVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class IngestionExceptionVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof IngestionException && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param IngestionException $exception
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $exception, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                // Grant VIEW access only to users with the Developer role.
                return $this->userHasRole($user, ['Developer']);
                break;
        }

        return false;
    }
}
