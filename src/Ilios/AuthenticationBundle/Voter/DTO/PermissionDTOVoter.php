<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\CoreBundle\Entity\DTO\PermissionDTO;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class PermissionDTOVoter
 */
class PermissionDTOVoter extends AbstractVoter
{
    /**
     * @inheritdoc
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof PermissionDTO && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param PermissionDTO $requestedPermission
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $requestedPermission, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return (
                    $user->getId() === $requestedPermission->user
                    || $user->hasRole(['Developer'])
                );
                break;
        }

        return false;
    }
}
