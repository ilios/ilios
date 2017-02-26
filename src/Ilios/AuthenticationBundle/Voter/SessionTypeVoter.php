<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\SessionTypeInterface;
use Ilios\CoreBundle\Entity\Manager\PermissionManager;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class SessionTypeVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class SessionTypeVoter extends AbstractVoter
{
    /**
     * @var PermissionManager
     */
    protected $permissionManager;

    /**
     * @param PermissionManager $permissionManager
     */
    public function __construct(PermissionManager $permissionManager)
    {
        $this->permissionManager = $permissionManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof SessionTypeInterface && in_array($attribute, array(
            self::CREATE, self::VIEW, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param SessionTypeInterface $sessionType
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $sessionType, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            // grant VIEW privileges
            // do not impose any restrictions.
            case self::VIEW:
                return true;
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                // grant CREATE, EDIT and DELETE privileges
                // if the user has the 'Developer' role
                // - and -
                //   if the user's primary school is the session type's owning school
                //   - or -
                //   if the user has WRITE rights on the session type's owning school
                // via the permissions system.
                return (
                    $this->userHasRole($user, ['Developer'])
                    && (
                        $this->schoolsAreIdentical($sessionType->getSchool(), $user->getSchool())
                        || $this->permissionManager->userHasWritePermissionToSchool(
                            $user,
                            $sessionType->getSchool()->getId()
                        )
                    )
                );
                break;
        }

        return false;
    }
}
