<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class CurriculumInventorySequenceBlockVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class CurriculumInventorySequenceBlockVoter extends CurriculumInventoryReportVoter
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

    /**
     * {@inheritdoc}
     */
    protected function isCreateGranted($report, $user)
    {
        // HALT!
        // Cannot create a sequence block once the parent report has been exported.
        if ($report->getExport()) {
            return false;
        }
        return parent::isCreateGranted($report, $user);
    }
}
