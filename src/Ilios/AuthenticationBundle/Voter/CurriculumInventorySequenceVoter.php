<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\CurriculumInventorySequenceInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class CurriculumInventorySequenceVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class CurriculumInventorySequenceVoter extends CurriculumInventoryReportVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\CurriculumInventorySequenceInterface');
    }

    /**
     * @param string $attribute
     * @param CurriculumInventorySequenceInterface $sequence
     * @param UserInterface $user
     * @return bool
     */
    protected function isGranted($attribute, $sequence, $user = null)
    {
        return parent::isGranted($attribute, $sequence->getReport(), $user);
    }

    /**
     * {@inheritdoc}
     */
    protected function isCreateGranted($report, $user)
    {
        return parent::isEditGranted();
    }
}
