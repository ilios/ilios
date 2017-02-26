<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\CurriculumInventoryExportInterface;
use Ilios\CoreBundle\Entity\Manager\PermissionManager;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class CurriculumInventoryExportVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class CurriculumInventoryExportVoter extends AbstractVoter
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
        return $subject instanceof CurriculumInventoryExportInterface && in_array($attribute, array(
            self::VIEW, self::CREATE
        ));
    }

    /**
     * @param string $attribute
     * @param CurriculumInventoryExportInterface $export
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $export, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::CREATE:
                // Only grant VIEW permissions to users with at least one of
                // 'Course Director' and 'Developer' roles.
                // - and -
                // the user must be associated with the school owning the parent report's program
                // either by its primary school attribute
                //     - or - by WROTE rights for the school
                // via the permissions system.
                return (
                    $this->userHasRole($user, ['Course Director', 'Developer'])
                    && (
                        $this->schoolsAreIdentical($user->getSchool(), $export->getReport()->getSchool())
                        || $this->permissionManager->userHasWritePermissionToSchool(
                            $user,
                            $export->getReport()->getSchool()->getId()
                        ))
                );
            case self::VIEW:
                // Only grant VIEW permissions to users with at least one of
                // 'Course Director' and 'Developer' roles.
                // - and -
                // the user must be associated with the school owning the parent report's program
                // either by its primary school attribute
                //     - or - by READ rights for the school
                // via the permissions system.
                return (
                    $this->userHasRole($user, ['Course Director', 'Developer'])
                    && (
                        $this->schoolsAreIdentical($user->getSchool(), $export->getReport()->getSchool())
                        || $this->permissionManager->userHasReadPermissionToSchool(
                            $user,
                            $export->getReport()->getSchool()->getId()
                        ))
                );
                break;
        }

        return false;
    }
}
