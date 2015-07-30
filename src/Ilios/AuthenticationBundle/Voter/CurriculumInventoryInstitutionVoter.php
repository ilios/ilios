<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\CurriculumInventoryInstitutionInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class CurriculumInventoryInstitutionVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class CurriculumInventoryInstitutionVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\CurriculumInventoryInstitutionInterface');
    }

    /**
     * @param string $attribute
     * @param CurriculumInventoryInstitutionInterface $institution
     * @param UserInterface $user
     * @return bool
     */
    protected function isGranted($attribute, $institution, $user = null)
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
