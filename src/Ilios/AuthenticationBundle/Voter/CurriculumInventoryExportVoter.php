<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\CurriculumInventoryExportInterface;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class CurriculumInventoryExportVoter
 */
class CurriculumInventoryExportVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof CurriculumInventoryExportInterface && in_array($attribute, array(
            self::VIEW, self::CREATE
        ));
    }

    /**
     * @param string $attribute
     * @param CurriculumInventoryExportInterface $export
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $export, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::CREATE:
                // Only grant VIEW permissions to users with at least one of
                // 'Course Director' and 'Developer' roles.
                // - and -
                // the user must be associated with the school owning the parent report's program
                // either by its primary school attribute
                //     - or - by WROTE rights for the school
                // via the permissions system.
                return (
                    $user->hasRole(['Course Director', 'Developer'])
                    && (
                        $user->isThePrimarySchool($export->getReport()->getSchool())
                        || $user->hasWritePermissionToSchool($export->getReport()->getSchool()->getId())
                    )
                );
            case self::VIEW:
                // Only grant VIEW permissions to users with at least one of
                // 'Course Director' and 'Developer' roles.
                // - and -
                // the user must be associated with the school owning the parent report's program
                // either by its primary school attribute
                //     - or - by READ rights for the school
                // via the permissions system.
                return (
                    $user->hasRole(['Course Director', 'Developer'])
                    && (
                        $user->isThePrimarySchool($export->getReport()->getSchool())
                        || $user->hasReadPermissionToSchool($export->getReport()->getSchool()->getId())
                    )
                );
                break;
        }

        return false;
    }
}
