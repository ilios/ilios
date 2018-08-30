<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use AppBundle\Service\TemporaryFileSystem as FileSystem;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class TemporaryFileSystem
 */
class TemporaryFileSystem extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof FileSystem && in_array($attribute, array(self::CREATE));
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

        if ($user->isRoot()) {
            return true;
        }

        return $user->performsNonLearnerFunction();
    }
}
