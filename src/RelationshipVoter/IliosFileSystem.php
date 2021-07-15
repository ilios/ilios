<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Service\IliosFileSystem as FileSystem;
use App\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class IliosFileSystem extends Voter
{
    public const CREATE_TEMPORARY_FILE = 'create_temporary_file';
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof FileSystem && $attribute == self::CREATE_TEMPORARY_FILE;
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
