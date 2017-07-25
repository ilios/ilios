<?php

namespace Ilios\AuthenticationBundle\Voter\Entity;

use Ilios\AuthenticationBundle\Voter\AbstractVoter;

use Ilios\CoreBundle\Entity\Manager\ProgramYearStewardManager;
use Ilios\CoreBundle\Entity\ProgramYearInterface;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class ProgramYearEntityVoter
 */
class ProgramYearEntityVoter extends AbstractVoter
{
    /**
     * @var ProgramYearStewardManager
     */
    protected $stewardManager;

    /**
     * @param ProgramYearStewardManager $stewardManager
     */
    public function __construct(
        ProgramYearStewardManager $stewardManager
    ) {
        $this->stewardManager = $stewardManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof ProgramYearInterface && in_array($attribute, array(
            self::CREATE, self::VIEW, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param ProgramYearInterface $programYear
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $programYear, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                return $this->isViewGranted($programYear, $user);
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                return $this->isWriteGranted($programYear, $user);
                break;
        }

        return false;
    }

    /**
     * @param ProgramYearInterface $programYear
     * @param SessionUserInterface $user
     * @return bool
     */
    protected function isViewGranted($programYear, $user)
    {
        // do not enforce special views permissions.
        return true;
    }

    /**
     * @param ProgramYearInterface $programYear
     * @param SessionUserInterface $user
     * @return bool
     */
    protected function isWriteGranted(ProgramYearInterface $programYear, $user)
    {
        // the given user is granted CREATE/EDIT/DELETE permissions on the given program year
        // when at least one of the following statements is true
        // 1. The user's primary school is the same as the parent program's owning school
        //    and the user has at least one of 'Course Director' and 'Developer' role.
        // 2. The user has WRITE permissions on the parent program's owning school
        //    and the user has at least one of 'Course Director' and 'Developer' role.
        // 3. The user's primary school matches at least one of the schools owning the
        //    program years' stewarding department,
        //    and the user has at least one of 'Course Director' and 'Developer' role.
        // 4. The user has WRITE permissions on the parent program.
        return (
            (
                $user->hasRole(['Course Director', 'Developer'])
                && (
                    $user->isThePrimarySchool($programYear->getSchool())
                    || $user->hasWritePermissionToSchool($programYear->getSchool()->getId())
                )
            )
            || $user->hasWritePermissionToProgram($programYear->getProgram()->getId())
        );
    }
}
