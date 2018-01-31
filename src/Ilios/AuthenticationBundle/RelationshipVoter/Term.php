<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\TermInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class Term extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        if ($this->abstain) {
            return false;
        }

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

        switch ($attribute) {
            case self::VIEW:
                return true;
                break;
            case self::CREATE:
                return $this->permissionChecker->canCreateTerm(
                    $user,
                    $subject->getVocabulary()->getSchool()->getId()
                );
                break;
            case self::EDIT:
                return $this->permissionChecker->canUpdateTerm(
                    $user,
                    $subject->getVocabulary()->getSchool()->getId()
                );
                break;
            case self::DELETE:
                return $this->permissionChecker->canDeleteTerm(
                    $user,
                    $subject->getVocabulary()->getSchool()->getId()
                );
                break;
        }

        return false;
    }
}
