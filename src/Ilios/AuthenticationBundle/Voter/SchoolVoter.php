<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\Manager\PermissionManagerInterface;
use Ilios\CoreBundle\Entity\SchoolInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class SchoolVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class SchoolVoter extends AbstractVoter
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
    protected function supports($attribute, $subject)
    {
        return $subject instanceof SchoolInterface && in_array($attribute, array(
            self::VIEW, self::CREATE, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param SchoolInterface $school
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $school, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                // at least one of these must be true.
                // 1. the given user has developer role
                // 2. the given user has explicit read permissions to the given school
                // 3. the given user has explicit read permissions to at least one course in the given school.
                // 4. the given user is a learner,instructor or director in courses of the given school.
                return (
                    $this->userHasRole($user, ['Developer'])
                    || $this->permissionManager->userHasReadPermissionToSchool($user, $school->getId())
                    || $this->permissionManager->userHasReadPermissionToCoursesInSchool($user, $school)
                    || $user->getAllSchools()->contains($school)
                );
                break;
            case self::CREATE:
                // only developers can create schools.
                return $this->userHasRole($user, ['Developer']);
                break;
            case self::EDIT:
            case self::DELETE:
                // Only grant EDIT and DELETE permissions if the user has the 'Developer' role.
                // - and -
                // the user must be associated with the given school,
                // either by its primary school attribute
                //     - or - by WRITE rights for the school
                // via the permissions system.
                return (
                    $this->userHasRole($user, ['Developer'])
                    && (
                        $this->schoolsAreIdentical($school, $user->getSchool())
                        || $this->permissionManager->userHasWritePermissionToSchool($user, $school->getId())
                    )
                );
                break;
        }

        return false;
    }
}
