<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\CompetencyInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class CompetencyVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class CompetencyVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\CompetencyInterface');
    }

    /**
     * @param string $attribute
     * @param CompetencyInterface $competency
     * @param UserInterface $user
     * @return bool
     */
    protected function isGranted($attribute, $competency, $user = null)
    {
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return true;
                break;
            case self::EDIT:
            case self::DELETE:
                if ($competency->getSchool()->getId() === $user->getPrimarySchool()) {
                    return $this->userHasRole($user, ['Developer', 'Faculty',]);
                }
                break;
        }

        return false;
    }
}
