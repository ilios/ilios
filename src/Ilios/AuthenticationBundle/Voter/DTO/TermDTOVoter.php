<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\AuthenticationBundle\Voter\Entity\TermEntityVoter;
use Ilios\CoreBundle\Entity\DTO\TermDTO;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class TermDTOVoter
 */
class TermDTOVoter extends TermEntityVoter
{
    /**
     * @inheritdoc
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof TermDTO && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param TermDTO $requestedTerm
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $requestedTerm, TokenInterface $token)
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
