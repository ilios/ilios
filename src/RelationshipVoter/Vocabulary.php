<?php

declare(strict_types=1);

namespace App\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Entity\VocabularyInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class Vocabulary extends AbstractVoter
{
    protected function supports($attribute, $subject): bool
    {
        return $subject instanceof VocabularyInterface
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
            self::CREATE => $this->permissionChecker->canCreateVocabulary($user, $subject->getSchool()->getId()),
            self::EDIT => $this->permissionChecker->canUpdateVocabulary(
                $user,
                $subject->getSchool()->getId()
            ),
            self::DELETE => $this->permissionChecker->canDeleteVocabulary(
                $user,
                $subject->getSchool()->getId()
            ),
            default => false,
        };
    }
}
