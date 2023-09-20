<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\UserMaterial as Material;
use App\Classes\SessionUserInterface;
use App\Classes\VoterPermissions;
use App\Entity\LearningMaterialStatusInterface;
use App\Service\SessionUserPermissionChecker;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserMaterial extends AbstractVoter
{
    public function __construct(SessionUserPermissionChecker $permissionChecker)
    {
        parent::__construct(
            $permissionChecker,
            Material::class,
            [
                VoterPermissions::VIEW,
            ]
        );
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        // root user can see all user events
        if ($user->isRoot()) {
            return true;
        }

        // Deny access to LMs that are 'in draft' if the current user
        // does not perform a non-learner function.
        return LearningMaterialStatusInterface::IN_DRAFT !== $subject->status
            || $user->performsNonLearnerFunction();
    }
}
