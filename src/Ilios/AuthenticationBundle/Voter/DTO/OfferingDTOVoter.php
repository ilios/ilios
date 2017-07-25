<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\CoreBundle\Entity\DTO\OfferingDTO;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class OfferingDTOVoter
 */
class OfferingDTOVoter extends CourseDTOVoter
{
    /**
     * @inheritdoc
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof OfferingDTO && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param OfferingDTO $offering
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $offering, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return $this->isViewGranted($offering->course, $offering->school, $user);
                break;
        }
        return false;
    }
}
