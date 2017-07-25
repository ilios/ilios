<?php

namespace Ilios\AuthenticationBundle\Voter\Entity;

use Ilios\CoreBundle\Entity\LearnerGroupInterface;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Ilios\AuthenticationBundle\Voter\AbstractVoter;

/**
 * Class LearnerGroupEntityVoter
 */
class LearnerGroupEntityVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof LearnerGroupInterface && in_array($attribute, array(
            self::VIEW, self::CREATE, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param LearnerGroupInterface $group
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $group, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                // do not enforce special views permissions on learner groups.
                return true;
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                // grant CREATE, EDIT and DELETE privileges if at least one of the following
                // statements is true:
                // 1. the user's primary school is the group's owning school
                //    and the user has at least one of the 'Course Director' and 'Developer' roles.
                // 2. the user has WRITE rights on the group's owning school via the permissions system
                //    and the user has at least one of the 'Course Director' and 'Developer' roles.
                // 3. the user has WRITE rights to the group's owning program.
                return (
                    $user->hasRole(['Course Director', 'Developer'])
                    && (
                        $user->isThePrimarySchool($group->getSchool())
                        || $user->hasWritePermissionToSchool($group->getSchool()->getId())
                    )
                    || $user->hasWritePermissionToProgram($group->getProgram()->getId())
                );
                break;
        }
        return false;
    }
}
