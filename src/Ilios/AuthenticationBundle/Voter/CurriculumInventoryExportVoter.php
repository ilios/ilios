<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\CurriculumInventoryExportInterface;
use Ilios\CoreBundle\Entity\Manager\PermissionManagerInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class CurriculumInventoryExportVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class CurriculumInventoryExportVoter extends AbstractVoter
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
        return array('Ilios\CoreBundle\Entity\CurriculumInventoryExportInterface');
    }

    /**
     * @param string $attribute
     * @param CurriculumInventoryExportInterface $export
     * @param UserInterface $user
     * @return bool
     */
    protected function isGranted($attribute, $export, $user = null)
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
                    && ($user->getPrimarySchool() === $export->getReport()->getProgram()->getOwningSchool()
                        || $this->permissionManager->userHasReadPermissionToSchool(
                            $user,
                            $export->getReport()->getProgram()->getOwningSchool()
                        ))
                );
                break;
            case self::EDIT:
            case self::DELETE:
                // HALT!
                // Exports cannot be edited or deleted.
                return false;
        }

        return false;
    }
}
