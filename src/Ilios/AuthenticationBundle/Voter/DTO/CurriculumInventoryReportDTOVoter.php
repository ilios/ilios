<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\CoreBundle\Entity\DTO\CurriculumInventoryReportDTO;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class CurriculumInventoryReportDTOVoter
 */
class CurriculumInventoryReportDTOVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof CurriculumInventoryReportDTO && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param CurriculumInventoryReportDTO $curriculumInventoryReport
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $curriculumInventoryReport, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return $this->isViewGranted($curriculumInventoryReport->school, $user);
                break;
        }
        return false;
    }

    /**
     * @param integer $schoolId
     * @param SessionUserInterface $sessionUser
     * @return bool
     */
    protected function isViewGranted($schoolId, SessionUserInterface $sessionUser)
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
                $sessionUser->getSchoolId() === $schoolId
                || $sessionUser->hasReadPermissionToSchool($schoolId)
            )
        );
    }
}
