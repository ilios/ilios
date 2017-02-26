<?php

namespace Ilios\AuthenticationBundle\Voter\Entity;

use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\CoreBundle\Entity\AlertChangeTypeInterface;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class AlertChangeTypeEntityVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class AlertChangeTypeEntityVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof AlertChangeTypeInterface && in_array($attribute, array(
            self::CREATE, self::VIEW, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param AlertChangeTypeInterface $type
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $type, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        // all authenticated users can view change types,
        // but only developers can create/modify/delete them directly.
        switch ($attribute) {
            case self::VIEW:
                return true;
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                return $this->userHasRole($user, ['Developer']);
                break;
        }

        return false;
    }
}
