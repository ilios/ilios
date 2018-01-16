<?php

namespace Ilios\AuthenticationBundle\Voter\Entity;

use Ilios\CoreBundle\Entity\CurriculumInventorySequenceInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class CurriculumInventorySequenceEntityVoter
 */
class CurriculumInventorySequenceEntityVoter extends CurriculumInventoryReportEntityVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        if ($this->abstain) {
            return false;
        }
        
        return $subject instanceof CurriculumInventorySequenceInterface && in_array($attribute, array(
            self::VIEW, self::CREATE, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param CurriculumInventorySequenceInterface $sequence
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $sequence, TokenInterface $token)
    {
        return parent::voteOnAttribute($attribute, $sequence->getReport(), $token);
    }
}
