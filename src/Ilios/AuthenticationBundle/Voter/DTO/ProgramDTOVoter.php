<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\CoreBundle\Entity\DTO\ProgramDTO;
use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class ProgramDTOVoter
 * @package Ilios\AuthenticationBundle\Voter\DTO
 */
class ProgramDTOVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof ProgramDTO && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param ProgramDTO $programDTO
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $programDTO, TokenInterface $token)
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
