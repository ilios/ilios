<?php

namespace Ilios\AuthenticationBundle\Voter\Entity;

use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\CoreBundle\Entity\CurriculumInventoryInstitutionInterface;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class CurriculumInventoryInstitutionVoter
 */
class CurriculumInventoryInstitutionEntityVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof CurriculumInventoryInstitutionInterface && in_array($attribute, array(
            self::VIEW, self::CREATE, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param CurriculumInventoryInstitutionInterface $institution
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $institution, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                // Only grant VIEW permissions to users with at least one of
                // 'Course Director' and 'Developer' roles.
                // - and -
                // the user must be associated with the institution's school
                // either by its primary school attribute
                //     - or - by READ rights for the school
                // via the permissions system.
                return (
                    $user->hasRole(['Course Director', 'Developer'])
                    && (
                        $user->isThePrimarySchool($institution->getSchool())
                        || $user->hasReadPermissionToSchool($institution->getSchool()->getId())
                    )
                );
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                // Only grant CREATE, EDIT and DELETE permissions to users with at least one of
                // 'Course Director' and 'Developer' roles.
                // - and -
                // the user must be associated with the institution's school
                // either by its primary school attribute
                //     - or - by WRITE rights for the school
                // via the permissions system.
                return (
                    $user->hasRole(['Course Director', 'Developer'])
                    && (
                        $user->isThePrimarySchool($institution->getSchool())
                        || $user->hasWritePermissionToSchool($institution->getSchool()->getId())
                    )
                );
                break;
        }

        return false;
    }
}
