<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSessionInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class CurriculumInventorySequenceBlockSessionVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class CurriculumInventorySequenceBlockSessionVoter extends AbstractVoter
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
