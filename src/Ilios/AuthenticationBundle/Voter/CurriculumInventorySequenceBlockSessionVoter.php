<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSessionInterface;
use Ilios\CoreBundle\Entity\Manager\PermissionManagerInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class CurriculumInventorySequenceBlockSessionVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class CurriculumInventorySequenceBlockSessionVoter extends AbstractVoter
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
        return array('Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockSessionInterface');
    }

    /**
     * @param string $attribute
     * @param CurriculumInventorySequenceBlockSessionInterface $session
     * @param UserInterface $user
     * @return bool
     */
    protected function isGranted($attribute, $session, $user = null)
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
                    && ($user->getPrimarySchool()
                        === $session->getSequenceBlock()->getReport()->getProgram()->getOwningSchool()
                        || $this->permissionManager->userHasReadPermissionToSchool(
                            $user,
                            $session->getSequenceBlock()->getReport()->getProgram()->getOwningSchool()
                        ))
                );
                break;
            case self::EDIT:
            case self::DELETE:
                // HALT!
                // Sequence blocks cannot be edited or deleted once their parent report have been exported.
                if ($session->getSequenceBlock()->getReport()->getExport()) {
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
                    && ($user->getPrimarySchool()
                        === $session->getSequenceBlock()->getReport()->getProgram()->getOwningSchool()
                        || $this->permissionManager->userHasWritePermissionToSchool(
                            $user,
                            $session->getSequenceBlock()->getReport()->getProgram()->getOwningSchool()
                        ))
                );
                break;
        }

        return false;
    }
}
