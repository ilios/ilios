<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Traits\ArchivableEntityInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Class ArchivableVoter
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
    protected function supports($attribute, $subject)
    {
        return $subject instanceof ArchivableEntityInterface && in_array($attribute, array(self::MODIFY));
    }

    /**
     * @param string $attribute
     * @param ArchivableEntityInterface $archivable
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $archivable, TokenInterface $token)
    {
        if (self::MODIFY === $attribute) {
            return !$archivable->isArchived();
        }

        return false;
    }
}
