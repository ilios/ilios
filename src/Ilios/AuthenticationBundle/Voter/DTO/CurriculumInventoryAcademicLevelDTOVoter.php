<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\DTO\CurriculumInventoryAcademicLevelDTO;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class CurriculumInventoryAcademicLevelDTOVoter
 */
class CurriculumInventoryAcademicLevelDTOVoter extends CurriculumInventoryReportDTOVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof CurriculumInventoryAcademicLevelDTO && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param CurriculumInventoryAcademicLevelDTO $curriculumInventoryAcademicLevel
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $curriculumInventoryAcademicLevel, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return $this->isViewGranted($curriculumInventoryAcademicLevel->school, $user);
                break;
        }
        return false;
    }
}
