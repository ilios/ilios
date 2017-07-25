<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\CoreBundle\Entity\DTO\InstructorGroupDTO;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class InstructorGroupDTOVoter
 */
class InstructorGroupDTOVoter extends AbstractVoter
{
    /**
     * @inheritdoc
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof InstructorGroupDTO && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param InstructorGroupDTO $instructorGroup
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $instructorGroup, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                // should be visible to any authenticated user in the system.
                // do not enforce any special permissions for viewing them.
                return true;
                break;
        }
        return false;
    }
}
