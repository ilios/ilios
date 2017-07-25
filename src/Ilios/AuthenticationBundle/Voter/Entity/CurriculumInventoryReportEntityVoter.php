<?php

namespace Ilios\AuthenticationBundle\Voter\Entity;

use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class CurriculumInventoryReportEntityVoter
 */
class CurriculumInventoryReportEntityVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof CurriculumInventoryReportInterface && in_array($attribute, array(
            self::VIEW, self::CREATE, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param CurriculumInventoryReportInterface $report
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $report, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return $this->isViewGranted($report, $user);
                break;
            case self::CREATE:
                return $this->isCreateGranted($report, $user);
                break;
            case self::EDIT:
                return $this->isEditGranted($report, $user);
                break;
            case self::DELETE:
                return $this->isDeleteGranted($report, $user);
        }

        return false;
    }

    /**
     * @param CurriculumInventoryReportInterface $report
     * @param SessionUserInterface $sessionUser
     * @return bool
     */
    protected function isViewGranted(CurriculumInventoryReportInterface $report, SessionUserInterface $sessionUser)
    {
        // Only grant VIEW permissions to users with at least one of
        // 'Course Director' and 'Developer' roles.
        // - and -
        // the user must be associated with the school owning the report's program
        // either by its primary school attribute
        //     - or - by READ rights for the school
        // via the permissions system.
        return (
            $sessionUser->hasRole(['Course Director', 'Developer'])
            && (
                $sessionUser->isThePrimarySchool($report->getSchool())
                || $sessionUser->hasReadPermissionToSchool($report->getSchool()->getId())
            )
        );
    }

    /**
     * @param CurriculumInventoryReportInterface $report
     * @param SessionUserInterface $sessionUser
     * @return bool
     */
    protected function isEditGranted(CurriculumInventoryReportInterface $report, SessionUserInterface $sessionUser)
    {
        // HALT!
        // Reports cannot be edited once they have been exported.
        if ($report->getExport()) {
            return false;
        }
        return $this->isCreateGranted($report, $sessionUser);
    }

    /**
     * @param CurriculumInventoryReportInterface $report
     * @param SessionUserInterface $sessionUser
     * @return bool
     */
    protected function isCreateGranted(CurriculumInventoryReportInterface $report, SessionUserInterface $sessionUser)
    {
        // HALT!
        // Cannot create anything once the report has been exported.
        // This could never happen for a Report itself,
        // but lots of the other CI voters depend on this one.
        if ($report->getExport()) {
            return false;
        }

        // Only grant CREATE, permissions to users with at least one of
        // 'Course Director' and 'Developer' roles.
        // - and -
        // the user must be associated with the school owning the report's program
        // either by its primary school attribute
        //     - or - by WRITE rights for the school
        // via the permissions system.
        return (
            $sessionUser->hasRole(['Course Director', 'Developer'])
            && (
                $sessionUser->isThePrimarySchool($report->getSchool())
                || $sessionUser->hasWritePermissionToSchool($report->getSchool()->getId())
            )
        );
    }

    /**
     * @param CurriculumInventoryReportInterface $report
     * @param SessionUserInterface $sessionUser
     * @return bool
     */
    protected function isDeleteGranted(CurriculumInventoryReportInterface $report, SessionUserInterface $sessionUser)
    {
        return $this->isEditGranted($report, $sessionUser);
    }
}
