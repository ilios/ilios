<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevelInterface;
use Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class CurriculumInventoryAcademicLevelVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class CurriculumInventoryAcademicLevelVoter extends CurriculumInventoryReportVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof CurriculumInventoryAcademicLevelInterface && in_array($attribute, array(
            self::CREATE, self::VIEW, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param CurriculumInventoryAcademicLevelInterface $level
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $level, TokenInterface $token)
    {
        return parent::voteOnAttribute($attribute, $level->getReport(), $token);
    }

    /**
     * {@inheritdoc}
     */
    protected function isCreateGranted(CurriculumInventoryReportInterface $report, SessionUserInterface $sessionUser)
    {
        // HALT!
        // Cannot create an academic level once the parent report has been exported.
        if ($report->getExport()) {
            return false;
        }
        return parent::isCreateGranted($report, $sessionUser);
    }
}
