<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class CurriculumInventorySequenceVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class CurriculumInventorySequenceVoter extends CurriculumInventoryReportVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
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

    /**
     * {@inheritdoc}
     */
    protected function isCreateGranted(CurriculumInventoryReportInterface $report, SessionUserInterface $user)
    {
        // HALT!
        // Cannot create a sequence once the parent report has been exported.
        if ($report->getExport()) {
            return false;
        }
        return parent::isCreateGranted($report, $user);
    }
}
