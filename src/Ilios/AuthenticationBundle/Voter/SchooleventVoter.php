<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Classes\SchoolEvent;
use Ilios\CoreBundle\Entity\Manager\PermissionManagerInterface;
use Ilios\CoreBundle\Entity\Manager\SchoolManagerInterface;
use Ilios\CoreBundle\Entity\SchoolInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class SchoolVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class SchooleventVoter extends AbstractVoter
{
    /**
     * @var PermissionManagerInterface
     */
    protected $permissionManager;

    /**
     * @var SchoolManagerInterface
     */
    protected $schoolManager;

    /**
     * @param PermissionManagerInterface $permissionManager
     * @param SchoolManagerInterface $schoolManager
     */
    public function __construct(PermissionManagerInterface $permissionManager, SchoolManagerInterface $schoolManager)
    {
        $this->permissionManager = $permissionManager;
        $this->schoolManager = $schoolManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function getSupportedAttributes()
    {
        return array(self::VIEW);
    }

    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Classes\SchoolEvent');
    }

    /**
     * @param string $attribute
     * @param SchoolEvent $event
     * @param UserInterface|null $user
     * @return bool
     */
    protected function isGranted($attribute, $event, $user = null)
    {
        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                // grant VIEW permissions if the event-owning school matches any of the given user's schools.
                $eventOwningSchool = $this->schoolManager->findSchoolBy(['id' => $event->school]);
                $userSchool = $user->getSchool();
                return (
                    $eventOwningSchool instanceof SchoolInterface
                    && $userSchool instanceof SchoolInterface
                    && ($eventOwningSchool->getId() === $userSchool->getId()
                        || $this->permissionManager->userHasReadPermissionToSchool($user, $eventOwningSchool)
                    )
                );
                break;
        }

        return false;
    }
}
