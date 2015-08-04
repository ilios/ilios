<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevelInterface;
use Ilios\CoreBundle\Entity\Manager\PermissionManagerInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class CurriculumInventoryAcademicLevelVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class CurriculumInventoryAcademicLevelVoter extends AbstractVoter
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
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevelInterface');
    }

    /**
     * @param string $attribute
     * @param CurriculumInventoryAcademicLevelInterface $level
     * @param UserInterface $user
     * @return bool
     */
    protected function isGranted($attribute, $level, $user = null)
    {
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
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
                    && ($user->getPrimarySchool() === $level->getReport()->getProgram()->getOwningSchool()
                        || $this->permissionManager->userHasReadPermissionToSchool(
                            $user,
                            $level->getReport()->getProgram()->getOwningSchool()
                        ))
                );
                break;
            case self::EDIT:
            case self::DELETE:
                // HALT!
                // Sequence blocks cannot be edited or deleted once their parent report have been exported.
                if ($level->getReport()->getExport()) {
                    return false;
                }
                // Only grant EDIT and DELETE permissions to users with at least one of
                // 'Course Director' and 'Developer' roles.
                // - and -
                // the user must be associated with the school owning the parent report's program
                // either by its primary school attribute
                //     - or - by WRITE rights for the school
                // via the permissions system.
                return (
                    $this->userHasRole($user, ['Course Director', 'Developer'])
                    && ($user->getPrimarySchool() === $level->getReport()->getProgram()->getOwningSchool()
                        || $this->permissionManager->userHasWritePermissionToSchool(
                            $user,
                            $level->getReport()->getProgram()->getOwningSchool()
                        ))
                );
                break;
        }

        return false;
    }
}
