<?php

namespace Ilios\AuthenticationBundle\Voter\DTO;

use Ilios\CoreBundle\Entity\Manager\PermissionManagerInterface;
use Ilios\CoreBundle\Entity\Manager\SchoolManagerInterface;
use Ilios\CoreBundle\Entity\DTO\SchoolDTO;
use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class SchoolDTOVoter
 * @package Ilios\AuthenticationBundle\Voter\DTO
 */
class SchoolDTOVoter extends AbstractVoter
{

    /**
     * @param PermissionManagerInterface $permissionManager
     * @param ProgramYearStewardManagerInterface $stewardManager
     */
    public function __construct(
        PermissionManagerInterface $permissionManager,
        SchoolManagerInterface $schoolManager
    ) {
        $this->permissionManager = $permissionManager;
        $this->schoolManager = $schoolManager;
    }

    /**
     * @inheritdoc
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof SchoolDTO && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param SchoolDTO $school
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $schoolDTO, TokenInterface $token)
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
                if ($this->userHasRole($user, ['Developer'])) {
                    return true;
                }
                $school = $this->schoolManager->findSchoolBy(['id' => $schoolDTO->id]);

                return ($this->permissionManager->userHasReadPermissionToSchool($user, $schoolDTO->id)
                    || $this->permissionManager->userHasReadPermissionToCoursesInSchool($user, $school)
                    || $user->getAllSchools()->contains($school)
                );
                break;
        }
        return false;
    }
}
