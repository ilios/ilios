<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface;
use Ilios\CoreBundle\Entity\Manager\PermissionManagerInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class CurriculumInventoryReportVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class CurriculumInventoryReportVoter extends AbstractVoter
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
        return array('Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface');
    }

    /**
     * @param string $attribute
     * @param CurriculumInventoryReportInterface $report
     * @param UserInterface $user
     * @return bool
     */
    protected function isGranted($attribute, $report, $user = null)
    {
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                // Only grant VIEW permissions to users with at least one of
                // 'Course Director' and 'Developer' roles.
                // - and -
                // the user must be associated with the school owning the report's program
                // either by its primary school attribute
                //     - or - by READ rights for the school
                // via the permissions system.
                return (
                    $this->userHasRole($user, ['Course Director', 'Developer'])
                    && ($user->getPrimarySchool() === $report->getProgram()->getOwningSchool()
                        || $this->permissionManager->userHasReadPermissionToSchool(
                            $user,
                            $report->getProgram()->getOwningSchool()
                        ))
                );
                break;
            case self::EDIT:
            case self::DELETE:
                // HALT!
                // Reports cannot be edited or deleted once they have been exported.
                if ($report->getExport()) {
                    return false;
                }
                // Only grant EDIT and DELETE permissions to users with at least one of
                // 'Course Director' and 'Developer' roles.
                // - and -
                // the user must be associated with the school owning the report's program
                // either by its primary school attribute
                //     - or - by WRITE rights for the school
                // via the permissions system.
                return (
                    $this->userHasRole($user, ['Course Director', 'Developer'])
                    && ($user->getPrimarySchool() === $report->getProgram()->getOwningSchool()
                        || $this->permissionManager->userHasWritePermissionToSchool(
                            $user,
                            $report->getProgram()->getOwningSchool()
                        ))
                );
                break;
        }

        return false;
    }
}
