<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class CurriculumInventorySequenceBlockSessionVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class CurriculumInventorySequenceBlockSessionVoter extends CurriculumInventoryReportVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof CurriculumInventorySequenceBlockSessionInterface && in_array($attribute, array(
            self::VIEW, self::CREATE, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param CurriculumInventorySequenceBlockSessionInterface $session
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $session, TokenInterface $token)
    {
        return parent::voteOnAttribute($attribute, $session->getSequenceBlock()->getReport(), $token);
    }

    /**
     * {@inheritdoc}
     */
    protected function isCreateGranted($report, $user)
    {
        // HALT!
        // Cannot create a sequence block session once the parent report has been exported.
        if ($report->getExport()) {
            return false;
        }
        return parent::isCreateGranted($report, $user);
    }
}
