<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\VoterPermissions;
use App\Service\SessionUserPermissionChecker;
use App\Service\TemporaryFileSystem as FileSystem;
use App\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class TemporaryFileSystem extends AbstractVoter
{
    public function __construct(SessionUserPermissionChecker $permissionChecker)
    {
        parent::__construct(
            $permissionChecker,
            FileSystem::class,
            [
                VoterPermissions::CREATE,
            ]
        );
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
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
