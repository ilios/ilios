<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\CurriculumInventorySequenceInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class CurriculumInventorySequenceVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class CurriculumInventorySequenceVoter extends AbstractVoter
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
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
            case self::EDIT:
            case self::DELETE:
                return $this->userHasRole($user, ['Course Director', 'Developer']);
                break;
        }

        return false;
    }
}
