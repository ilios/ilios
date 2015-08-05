<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevelInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class CurriculumInventoryAcademicLevelVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class CurriculumInventoryAcademicLevelVoter extends CurriculumInventoryReportVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevelInterface');
    }

    /**
     * @param string $attribute
     * @param CurriculumInventoryAcademicLevelInterface $level
     * @param UserInterface $user
     * @return bool
     */
    protected function isGranted($attribute, $level, $user = null)
    {
        return parent::isGranted($attribute, $level->getReport(), $user);
    }

    /**
     * {@inheritdoc}
     */
    protected function isCreateGranted($report, $user)
    {
        return parent::isEditGranted();
    }
}
