<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Classes\VoterPermissions;
use App\Entity\OfferingInterface;
use App\Service\SessionUserPermissionChecker;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class Offering extends AbstractVoter
{
    public function __construct(SessionUserPermissionChecker $permissionChecker)
    {
        parent::__construct(
            $permissionChecker,
            OfferingInterface::class,
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
            VoterPermissions::VIEW => $user->performsNonLearnerFunction(),
            VoterPermissions::EDIT,
            VoterPermissions::CREATE,
            VoterPermissions::DELETE => $this->permissionChecker->canUpdateSession(
                $user,
                $subject->getSession()
            ),
            default => false,
        };
    }
}
