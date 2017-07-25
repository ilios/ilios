<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Classes\SchoolEvent;
use Ilios\CoreBundle\Entity\Manager\SchoolManager;
use Ilios\CoreBundle\Entity\SchoolInterface;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class SchoolVoter
 */
class SchooleventVoter extends AbstractVoter
{
    /**
     * @var SchoolManager
     */
    protected $schoolManager;

    /**
     * @param SchoolManager $schoolManager
     */
    public function __construct(SchoolManager $schoolManager)
    {
        $this->schoolManager = $schoolManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof SchoolEvent && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param SchoolEvent $event
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $event, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof SessionUserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                // grant VIEW permissions if the event-owning school matches any of the given user's schools.
                // In addition, if the given user has NOT elevated privileges,
                // then do not grant access to view un-published events.
                /* @var SchoolInterface $eventOwningSchool */
                $eventOwningSchool = $this->schoolManager->findOneBy(['id' => $event->school]);
                if ($user->hasRole(['Faculty', 'Course Director', 'Developer'])) {
                    return $user->isThePrimarySchool($eventOwningSchool)
                    || $user->hasReadPermissionToSchool($eventOwningSchool->getId());
                } else {
                    return ((
                            $user->isThePrimarySchool($eventOwningSchool)
                            || $user->hasReadPermissionToSchool($eventOwningSchool->getId())
                        ) && $event->isPublished);
                }
                break;
        }
        return false;
    }
}
