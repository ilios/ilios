<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\DTO\SchoolDTO;
use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class SchoolDTOVoter
 */
class SchoolDTOVoter extends AbstractVoter
{

    /**
     * @inheritdoc
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof SchoolDTO && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param SchoolDTO $school
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $school, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }
        // this voter only supports view access, grant it to all authn. users.
        return true;
    }
}
