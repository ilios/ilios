<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Entity\CompetencyInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class Competency extends AbstractVoter
{
    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof CompetencyInterface
            && in_array($attribute, [self::CREATE, self::VIEW, self::EDIT, self::DELETE]);
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }
        if ($user->isRoot()) {
            return true;
        }
        return match ($attribute) {
            self::VIEW => true,
            self::CREATE => $this->permissionChecker->canCreateCompetency(
                $user,
                $subject->getSchool()->getId()
            ),
            self::EDIT => $this->permissionChecker->canUpdateCompetency(
                $user,
                $subject->getSchool()->getId()
            ),
            self::DELETE => $this->permissionChecker->canDeleteCompetency(
                $user,
                $subject->getSchool()->getId()
            ),
            default => false,
        };
    }
}
