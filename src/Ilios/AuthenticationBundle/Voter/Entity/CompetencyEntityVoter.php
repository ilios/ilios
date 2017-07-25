<?php

namespace Ilios\AuthenticationBundle\Voter\Entity;

use Ilios\CoreBundle\Entity\CompetencyInterface;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class CompetencyEntityVoter
 */
class CompetencyEntityVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof CompetencyInterface && in_array($attribute, array(
            self::CREATE, self::VIEW, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param CompetencyInterface $competency
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $competency, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                // do not enforce special view permissions.
                return true;
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                // grant CREATE, EDIT and DELETE privileges
                // if the user has the 'Developer' role
                // - and -
                //   if the user's primary school is the the competency's owning school
                //   - or -
                //   if the user has WRITE rights on the competency's owning school
                // via the permissions system.
                return ($user->hasRole(['Developer'])
                    && (
                        $user->isThePrimarySchool($competency->getSchool())
                        || $user->hasWritePermissionToSchool($competency->getSchool()->getId())
                    )
                );
                break;
        }

        return false;
    }
}
