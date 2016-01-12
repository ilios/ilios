<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\CohortInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class CohortVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class CohortVoter extends ProgramYearVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof CohortInterface && in_array($attribute, array(
            self::CREATE, self::VIEW, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param CohortInterface $cohort
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $cohort, TokenInterface $token)
    {
        $programYear = $cohort->getProgramYear();
        if (! $programYear) {
            return false;
        }

        return parent::voteOnAttribute($attribute, $programYear, $token);
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
