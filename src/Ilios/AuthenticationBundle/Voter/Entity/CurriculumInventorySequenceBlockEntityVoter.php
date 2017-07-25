<?php

namespace Ilios\AuthenticationBundle\Voter\Entity;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class CurriculumInventorySequenceBlockVoter
 */
class CurriculumInventorySequenceBlockEntityVoter extends CurriculumInventoryReportEntityVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof CurriculumInventorySequenceBlockInterface && in_array($attribute, array(
            self::VIEW, self::CREATE, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param CurriculumInventorySequenceBlockInterface $block
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $block, TokenInterface $token)
    {
        return parent::voteOnAttribute($attribute, $block->getReport(), $token);
    }
}
