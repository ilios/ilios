<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\VoterPermissions;
use App\Service\IliosFileSystem as FileSystem;
use App\Classes\SessionUserInterface;
use App\Service\SessionUserPermissionChecker;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class IliosFileSystem extends AbstractVoter
{
    public function __construct(SessionUserPermissionChecker $permissionChecker)
    {
        parent::__construct(
            $permissionChecker,
            FileSystem::class,
            [
                VoterPermissions::CREATE_TEMPORARY_FILE
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
