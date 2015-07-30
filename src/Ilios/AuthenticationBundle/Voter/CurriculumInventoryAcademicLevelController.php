<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevelInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class CurriculumInventoryAcademicLevelVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class CurriculumInventoryAcademicLevelVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevelInterface');
    }

    /**
     * @param string $attribute
     * @param CurriculumInventoryAcademicLevelInterface $level
     * @param UserInterface $user
     * @return bool
     */
    protected function isGranted($attribute, $level, $user = null)
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
