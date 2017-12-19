<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\TermInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class Term extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return $subject instanceof TermInterface
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

        if ($subject instanceof TermInterface) {
            return $this->voteOnEntity($attribute, $user, $subject);
        }

        return false;
    }

    protected function voteOnEntity(
        string $attribute,
        SessionUserInterface $sessionUser,
        TermInterface $term
    ): bool {
        switch ($attribute) {
            case self::VIEW:
                return $this->permissionChecker->canReadTerm(
                    $sessionUser,
                    $term->getVocabulary()->getSchool()->getId()
                );
                break;
            case self::CREATE:
                return $this->permissionChecker->canCreateTerm(
                    $sessionUser,
                    $term->getVocabulary()->getSchool()->getId()
                );
                break;
            case self::EDIT:
                return $this->permissionChecker->canUpdateTerm(
                    $sessionUser,
                    $term->getVocabulary()->getSchool()->getId()
                );
                break;
            case self::DELETE:
                return $this->permissionChecker->canDeleteTerm(
                    $sessionUser,
                    $term->getVocabulary()->getSchool()->getId()
                );
                break;
        }

        return false;
    }
}
