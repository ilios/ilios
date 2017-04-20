<?php

namespace Ilios\AuthenticationBundle\Voter\Entity;

use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\CoreBundle\Entity\AlertInterface;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class AlertEntityVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class AlertEntityVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof AlertInterface && in_array($attribute, array(
            self::CREATE, self::VIEW, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param AlertInterface $alert
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $alert, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        // all authenticated users can view alerts,
        // but only developers can create/modify/delete them directly.
        switch ($attribute) {
            case self::VIEW:
                return true;
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                return $user->hasRole(['Developer']);
                break;
        }

        return false;
    }
}
