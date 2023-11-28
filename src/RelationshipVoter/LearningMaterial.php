<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\VoterPermissions;
use App\Entity\LearningMaterialInterface;
use App\Classes\SessionUserInterface;
use App\Service\SessionUserPermissionChecker;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class LearningMaterial
 */
class LearningMaterial extends AbstractVoter
{
    public function __construct(SessionUserPermissionChecker $permissionChecker)
    {
        parent::__construct(
            $permissionChecker,
            LearningMaterialInterface::class,
            [
                VoterPermissions::CREATE,
                VoterPermissions::VIEW,
                VoterPermissions::EDIT,
                VoterPermissions::DELETE,
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
        return match ($attribute) {
            VoterPermissions::VIEW => true,
            VoterPermissions::CREATE,
            VoterPermissions::EDIT,
            VoterPermissions::DELETE => $user->performsNonLearnerFunction(),
            default => false,
        };
    }
}
