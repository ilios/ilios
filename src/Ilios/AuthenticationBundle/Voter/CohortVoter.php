<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\CohortInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class CohortVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class CohortVoter extends ProgramYearVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\CohortInterface');
    }

    /**
     * @param string $attribute
     * @param CohortInterface $cohort
     * @param UserInterface|null $user
     * @return bool
     */
    protected function isGranted($attribute, $cohort, $user = null)
    {
        return parent::isGranted($attribute, $cohort->getProgramYear(), $user);
    }

    /**
     * {@inheritdoc}
     */
    protected function isCreateGranted($programYear, $user)
    {
        return $this->isEditGranted($programYear, $user);
    }
}
