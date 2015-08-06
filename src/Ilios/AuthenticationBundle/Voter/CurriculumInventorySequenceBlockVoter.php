<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class CurriculumInventorySequenceBlockVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class CurriculumInventorySequenceBlockVoter extends CurriculumInventoryReportVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockInterface');
    }

    /**
     * @param string $attribute
     * @param CurriculumInventorySequenceBlockInterface $block
     * @param UserInterface $user
     * @return bool
     */
    protected function isGranted($attribute, $block, $user = null)
    {
        return parent::isGranted($attribute, $block->getReport(), $user);
    }

    /**
     * {@inheritdoc}
     */
    protected function isCreateGranted($report, $user)
    {
        return $this->isEditGranted();
    }
}
