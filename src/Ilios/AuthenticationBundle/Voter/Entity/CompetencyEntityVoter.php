<?php

namespace Ilios\AuthenticationBundle\Voter\Entity;

use Ilios\CoreBundle\Entity\CompetencyInterface;
use Ilios\CoreBundle\Entity\Manager\PermissionManager;
use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class CompetencyEntityVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class CompetencyEntityVoter extends AbstractVoter
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
        return $subject instanceof CompetencyInterface && in_array($attribute, array(
            self::CREATE, self::VIEW, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param CompetencyInterface $competency
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $competency, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                // do not enforce special view permissions.
                return true;
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                // grant CREATE, EDIT and DELETE privileges
                // if the user has the 'Developer' role
                // - and -
                //   if the user's primary school is the the competency's owning school
                //   - or -
                //   if the user has WRITE rights on the competency's owning school
                // via the permissions system.
                return ($this->userHasRole($user, ['Developer'])
                    && (
                        $this->schoolsAreIdentical($competency->getSchool(), $user->getSchool())
                        || $this->permissionManager->userHasWritePermissionToSchool(
                            $user,
                            $competency->getSchool()->getId()
                        )
                    )
                );
                break;
        }

        return false;
    }
}
