<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\ProgramInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class ProgramVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class ProgramVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\ProgramInterface');
    }

    /**
     * @param string $attribute
     * @param ProgramInterface $program
     * @param UserInterface|null $user
     * @return bool
     */
    protected function isGranted($attribute, $program, $user = null)
    {
        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                if ($program->getOwningSchool()->getId() === $user->getPrimarySchool()->getId()) {
                    return $this->userHasRole($user, ['Course Director', 'Developer', 'Faculty']);
                }
                break;
            case self::EDIT:
            case self::DELETE:
                if ($program->getOwningSchool->getId() === $user->getPrimarySchool()->getId()) {
                    return $this->userHasRole($user, ['Course Director', 'Developer']);
                }
                break;
        }

        return false;
    }
}
