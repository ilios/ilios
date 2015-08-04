<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\CurriculumInventorySequenceInterface;
use Ilios\CoreBundle\Entity\Manager\PermissionManagerInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class CurriculumInventorySequenceVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class CurriculumInventorySequenceVoter extends AbstractVoter
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
        return array('Ilios\CoreBundle\Entity\CurriculumInventorySequenceInterface');
    }

    /**
     * @param string $attribute
     * @param CurriculumInventorySequenceInterface $sequence
     * @param UserInterface $user
     * @return bool
     */
    protected function isGranted($attribute, $sequence, $user = null)
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
                    && ($user->getPrimarySchool() === $sequence->getReport()->getProgram()->getOwningSchool()
                        || $this->permissionManager->userHasReadPermissionToSchool($user, $sequence->getReport()->getProgram()->getOwningSchool()))
                );
                break;
            case self::EDIT:
            case self::DELETE:
                // HALT!
                // Sequences cannot be edited or deleted once their parent report have been exported.
                if ($sequence->getReport()->getExport()) {
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
                    && ($user->getPrimarySchool() === $sequence->getReport()->getProgram()->getOwningSchool()
                        || $this->permissionManager->userHasWritePermissionToSchool($user, $sequence->getReport()->getProgram()->getOwningSchool()))
                );
                break;
        }

        return false;
    }
}
