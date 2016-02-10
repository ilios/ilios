<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\CurriculumInventoryInstitutionInterface;
use Ilios\CoreBundle\Entity\Manager\PermissionManagerInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class CurriculumInventoryInstitutionVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class CurriculumInventoryInstitutionVoter extends AbstractVoter
{
    /**
     * @var PermissionManagerInterface
     */
    protected $permissionManager;

    /**
     * @param PermissionManagerInterface $permissionManager
     */
    public function __construct(PermissionManagerInterface $permissionManager)
    {
        $this->permissionManager = $permissionManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof CurriculumInventoryInstitutionInterface && in_array($attribute, array(
            self::VIEW, self::CREATE, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param CurriculumInventoryInstitutionInterface $institution
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $institution, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
            case self::EDIT:
            case self::DELETE:
                return $this->userHasRole($user, ['Course Director', 'Developer']);
                break;
        }

        switch ($attribute) {
            case self::VIEW:
                // Only grant VIEW permissions to users with at least one of
                // 'Course Director' and 'Developer' roles.
                // - and -
                // the user must be associated with the institution's school
                // either by its primary school attribute
                //     - or - by READ rights for the school
                // via the permissions system.
                return (
                    $this->userHasRole($user, ['Course Director', 'Developer'])
                    && (
                        $this->schoolsAreIdentical($user->getSchool(), $institution->getSchool())
                        || $this->permissionManager->userHasReadPermissionToSchool(
                            $user, $institution->getSchool()->getId()
                        )
                    )
                );
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                // Only grant CREATE, EDIT and DELETE permissions to users with at least one of
                // 'Course Director' and 'Developer' roles.
                // - and -
                // the user must be associated with the institution's school
                // either by its primary school attribute
                //     - or - by WRITE rights for the school
                // via the permissions system.
                return (
                    $this->userHasRole($user, ['Course Director', 'Developer'])
                    && (
                        $this->schoolsAreIdentical($user->getSchool(), $institution->getSchool())
                        || $this->permissionManager->userHasWritePermissionToSchool($user, $institution->getSchool()->getId())
                    )
                );
                break;
        }

        return false;
    }
}
