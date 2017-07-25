<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\DTO\CurriculumInventoryInstitutionDTO;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class CurriculumInventoryInstitutionDTOVoter
 */
class CurriculumInventoryInstitutionDTOVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof CurriculumInventoryInstitutionDTO && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param CurriculumInventoryInstitutionDTO $institution
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $institution, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        return (
            $user->hasRole(['Course Director', 'Developer'])
            && (
                $user->getSchoolId() === $institution->school
                || $user->hasReadPermissionToSchool($institution->school)
            )
        );
    }
}
