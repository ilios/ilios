<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\CoreBundle\Entity\DTO\AssessmentOptionDTO;
use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class AssessmentOptionDTOVoter
 * @package Ilios\AuthenticationBundle\Voter\DTO
 */
class AssessmentOptionDTOVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof AssessmentOptionDTO && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param AssessmentOptionDTO $assessmentOptionDto
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $assessmentOptionDto, TokenInterface $token)
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
