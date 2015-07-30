<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\School;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class SchoolVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class SchoolVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\SchoolInterface');
    }

    /**
     * @param string $attribute
     * @param School $school
     * @param UserInterface|null $user
     * @return bool
     */
    protected function isGranted($attribute, $school, $user = null)
    {
        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        switch ($attribute) {
            case self::VIEW:
                if ($school->getId() === $user->getPrimarySchool()->getId()) {
                    return true;
                }
                break;
            case self::EDIT:
            case self::DELETE:
                if ($school->getId() === $user->getPrimarySchool()->getId()) {
                    return $this->userHasRole($user, ['Course Director', 'Developer', 'Faculty']);
                }
                break;
        }

        return false;
    }
}
