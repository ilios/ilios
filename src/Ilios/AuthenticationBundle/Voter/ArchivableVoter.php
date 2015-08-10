<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\CoreBundle\Traits\ArchivableEntityInterface;
use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter as Voter;

/**
 * Class ArchivableVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class ArchivableVoter extends Voter
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
        return array('Ilios\CoreBundle\Traits\ArchivableEntityInterface');
    }

    /**
     * @param string $attribute
     * @param ArchivableEntityInterface $archivable
     * @param UserInterface|null $user
     * @return bool
     */
    protected function isGranted($attribute, $archivable, $user = null)
    {
        if (self::MODIFY === $attribute) {
            return ! $archivable->isArchived();
        }
        return false;
    }
}
