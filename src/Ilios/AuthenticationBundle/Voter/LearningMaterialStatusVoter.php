<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\LearningMaterialStatusInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class LearningMaterialStatusVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class LearningMaterialStatusVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\LearningMaterialStatusInterface');
    }

    /**
     * @param string $attribute
     * @param LearningMaterialStatusInterface $status
     * @param UserInterface $user
     * @return bool
     */
    protected function isGranted($attribute, $status, $user = null)
    {
        if (!$user instanceof UserInterface) {
            return false;
        }

        // all authenticated users can view LM statuses,
        // but only developers can create/modify/delete them directly.
        switch ($attribute) {
            case self::VIEW:
                return true;
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                return $this->userHasRole($user, ['Developer']);
                break;
        }

        return false;
    }
}
