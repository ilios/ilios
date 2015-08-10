<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\CoreBundle\Traits\LockableEntityInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter as Voter;

/**
 * Class LockableVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class LockableVoter extends Voter
{
    /**
     * @var string
     */
    const MODIFY = 'modify';

    /**
     * {@inheritdoc}
     */
    protected function getSupportedAttributes()
    {
        return array(self::MODIFY);
    }

    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Traits\LockableEntityInterface');
    }

    /**
     * @param string $attribute
     * @param LockableEntityInterface $lockable
     * @param UserInterface|null $user
     * @return bool
     */
    protected function isGranted($attribute, $lockable, $user = null)
    {
        if (self::MODIFY === $attribute) {
            return ! $lockable->isLocked();
        }
        return false;
    }
}
