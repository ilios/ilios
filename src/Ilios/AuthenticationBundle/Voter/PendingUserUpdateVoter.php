<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\CoreBundle\Entity\PendingUserUpdateInterface;

/**
 * Class PendingUserUpdateVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class PendingUserUpdateVoter extends UserVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return ['Ilios\CoreBundle\Entity\PendingUserUpdateInterface'];
    }

    /**
     * @param string $attribute
     * @param PendingUserUpdateInterface $pendingUserUpdate
     * @param UserInterface|null $user
     * @return bool
     */
    protected function isGranted($attribute, $pendingUserUpdate, $user = null)
    {
        // grant perms based on the user
        return parent::isGranted($attribute, $pendingUserUpdate->getUser(), $user);
    }
}
