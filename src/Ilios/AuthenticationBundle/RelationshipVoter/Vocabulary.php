<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\VocabularyInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class Vocabulary extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $subject instanceof VocabularyInterface
            && in_array(
                $attribute,
                [self::CREATE, self::VIEW, self::EDIT, self::DELETE]
            );
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }
        if ($user->isRoot()) {
            return true;
        }

        if ($subject instanceof VocabularyInterface) {
            return $this->voteOnEntity($attribute, $user, $subject);
        }

        return false;
    }

    protected function voteOnEntity(
        string $attribute,
        SessionUserInterface $sessionUser,
        VocabularyInterface $vocabulary
    ): bool {
        switch ($attribute) {
            case self::VIEW:
                return $this->permissionChecker->canReadVocabulary(
                    $sessionUser,
                    $vocabulary->getSchool()->getId()
                );
                break;
            case self::CREATE:
                return $this->permissionChecker->canCreateVocabulary($sessionUser, $vocabulary->getSchool()->getId());
                break;
            case self::EDIT:
                return $this->permissionChecker->canUpdateVocabulary(
                    $sessionUser,
                    $vocabulary->getSchool()->getId()
                );
                break;
            case self::DELETE:
                return $this->permissionChecker->canDeleteVocabulary(
                    $sessionUser,
                    $vocabulary->getSchool()->getId()
                );
                break;
        }

        return false;
    }
}
