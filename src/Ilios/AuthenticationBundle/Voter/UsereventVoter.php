<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\Manager\UserManagerInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\CoreBundle\Classes\UserEvent;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class SchoolVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class UsereventVoter extends AbstractVoter
{
    /**
     * @var UserManagerInterface
     */
    protected $userManager;

    /**
     * @param UserManagerInterface $userManager
     */
    public function __construct(UserManagerInterface $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function supports($attribute, $subject)
    {
        return $subject instanceof UserEvent && in_array($attribute, array(self::VIEW));
    }

    /**
     * @param string $attribute
     * @param UserEvent $event
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $event, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::VIEW:
                // Check if the event-owning user is the given user.
                // In addition, if the given user has NOT elevated privileges,
                // then do not grant access to view un-published events.
                if ($this->userHasRole($user, ['Faculty', 'Course Director', 'Developer'])) {
                    return $user->getId() === $event->user;
                } else {
                    return ($user->getId() == $event->user && $event->isPublished);
                }
                break;
        }
        return false;
    }
}
