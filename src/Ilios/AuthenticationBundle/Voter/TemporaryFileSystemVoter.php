<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Service\TemporaryFileSystem;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class TemporaryFileSystemVoter
 */
class TemporaryFileSystemVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof TemporaryFileSystem && in_array($attribute, array(self::CREATE));
    }

    /**
     * @param string $attribute
     * @param TemporaryFileSystem $fileSystem
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $fileSystem, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            // only user with Faculty/Course Director/Developer roles
            // have CREATE permissions to the temporary file system.
            case self::CREATE:
                return ($user->hasRole(['Faculty', 'Course Director', 'Developer']));
                break;
        }

        return false;
    }
}
