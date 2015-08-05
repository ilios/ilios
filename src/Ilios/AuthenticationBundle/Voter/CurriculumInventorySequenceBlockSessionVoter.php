<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSessionInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class CurriculumInventorySequenceBlockSessionVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class CurriculumInventorySequenceBlockSessionVoter extends CurriculumInventoryReportVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSessionInterface');
    }

    /**
     * @param string $attribute
     * @param CurriculumInventorySequenceBlockSessionInterface $session
     * @param UserInterface $user
     * @return bool
     */
    protected function isGranted($attribute, $session, $user = null)
    {
        return parent::isGranted($attribute, $session->getSequenceBlock()->getReport(), $user);
    }

    /**
     * {@inheritdoc}
     */
    protected function isCreateGranted($report, $user)
    {
        return parent::isEditGranted();
    }
}
