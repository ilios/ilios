<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\AssessmentOptionInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class AssessmentOptionVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class AssessmentOptionVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\AssessmentOptionInterface');
    }

    /**
     * @param string $attribute
     * @param AssessmentOptionInterface $option
     * @param UserInterface $user
     * @return bool
     */
    protected function isGranted($attribute, $option, $user = null)
    {
        if (!$user instanceof UserInterface) {
            return false;
        }

        // all authenticated users can view assessment options,
        // but only developers can modify/delete them directly.
        switch ($attribute) {
            case self::VIEW:
                return true;
                break;
            case self::EDIT:
            case self::DELETE:
                return $this->userHasRole($user, ['Developer']);
                break;
        }

        return false;
    }
}
