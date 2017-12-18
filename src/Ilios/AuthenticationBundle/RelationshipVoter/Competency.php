<?php

namespace Ilios\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\CompetencyInterface;
use Ilios\CoreBundle\Entity\DTO\CompetencyDTO;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class Competency extends AbstractVoter
{
    protected function supports($attribute, $subject)
    {
        return (
            ($subject instanceof CompetencyDTO && in_array($attribute, [self::VIEW])) or
            ($subject instanceof CompetencyInterface && in_array($attribute, [
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

        if ($subject instanceof CompetencyDTO) {
            return $this->voteOnDTO($user, $subject);
        }

        if ($subject instanceof CompetencyInterface) {
            return $this->voteOnEntity($attribute, $user, $subject);
        }

        return false;
    }

    protected function voteOnDTO(SessionUserInterface $sessionUser, CompetencyDTO $Competency): bool
    {
        return $this->permissionChecker->canReadCompetency($sessionUser, $Competency->school);
    }

    protected function voteOnEntity(
        string $attribute,
        SessionUserInterface $sessionUser,
        CompetencyInterface $Competency
    ): bool {
        switch ($attribute) {
            case self::VIEW:
                return $this->permissionChecker->canReadCompetency(
                    $sessionUser,
                    $Competency->getSchool()->getId()
                );
                break;
            case self::CREATE:
                return $this->permissionChecker->canCreateCompetency(
                    $sessionUser,
                    $Competency->getSchool()->getId()
                );
                break;
            case self::EDIT:
                return $this->permissionChecker->canUpdateCompetency(
                    $sessionUser,
                    $Competency->getSchool()->getId()
                );
                break;
            case self::DELETE:
                return $this->permissionChecker->canDeleteCompetency(
                    $sessionUser,
                    $Competency->getSchool()->getId()
                );
                break;
        }

        return false;
    }
}
