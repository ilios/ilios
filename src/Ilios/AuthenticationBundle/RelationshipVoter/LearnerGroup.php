<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\LearnerGroupInterface;
use Ilios\CoreBundle\Entity\DTO\LearnerGroupDTO;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class LearnerGroup extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return (
            ($subject instanceof LearnerGroupDTO && in_array($attribute, [self::VIEW])) or
            ($subject instanceof LearnerGroupInterface && in_array($attribute, [
                    self::CREATE, self::VIEW, self::EDIT, self::DELETE
                ]))
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

        if ($subject instanceof LearnerGroupDTO) {
            return $this->voteOnDTO($user, $subject);
        }

        if ($subject instanceof LearnerGroupInterface) {
            return $this->voteOnEntity($attribute, $user, $subject);
        }

        return false;
    }

    protected function voteOnDTO(SessionUserInterface $sessionUser, LearnerGroupDTO $learnerGroup): bool
    {
        return $this->permissionChecker->canReadLearnerGroup($sessionUser, $learnerGroup->school);
    }

    protected function voteOnEntity(
        string $attribute,
        SessionUserInterface $sessionUser,
        LearnerGroupInterface $learnerGroup
    ): bool {
        switch ($attribute) {
            case self::VIEW:
                return $this->permissionChecker->canReadLearnerGroup(
                    $sessionUser,
                    $learnerGroup->getSchool()->getId()
                );
                break;
            case self::CREATE:
                return $this->permissionChecker->canCreateLearnerGroup($sessionUser, $learnerGroup->getSchool()->getId());
                break;
            case self::EDIT:
                return $this->permissionChecker->canUpdateLearnerGroup(
                    $sessionUser,
                    $learnerGroup->getSchool()->getId()
                );
                break;
            case self::DELETE:
                return $this->permissionChecker->canDeleteLearnerGroup(
                    $sessionUser,
                    $learnerGroup->getSchool()->getId()
                );
                break;
        }

        return false;
    }
}
