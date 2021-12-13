<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Service\TemporaryFileSystem as FileSystem;
use App\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class TemporaryFileSystem
 */
class TemporaryFileSystem extends AbstractVoter
{
    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof FileSystem && $attribute == self::CREATE;
    }

    /**
     * @param string $attribute
     * @param TemporaryFileSystem $fileSystem
     * @param TokenInterface $token
     */
    protected function voteOnAttribute($attribute, $fileSystem, TokenInterface $token): bool
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
