<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\CoreBundle\Entity\DTO\SchoolDTO;
use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class SchoolDTOVoter
 * @package Ilios\AuthenticationBundle\Voter\DTO
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
        if (!$user instanceof UserInterface) {
            return false;
        }
        // this voter only supports view access, grant it to all authn. users.
        return true;
    }
}
