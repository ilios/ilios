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
        $programYear = $cohort->getProgramYear();
        if (! $programYear) {
            return false;
        }
        return parent::isGranted($attribute, $programYear, $user);
    }

    /**
     * {@inheritdoc}
     */
    protected function isWriteGranted($programYear, $user)
    {
        // prevent modifications and deletions of locked or archived program years
        if ($programYear->isLocked() || $programYear->isArchived()) {
            return false;
        }
        return parent::isWriteGranted($programYear, $user);
    }
}
