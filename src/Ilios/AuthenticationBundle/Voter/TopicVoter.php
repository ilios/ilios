<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\TopicInterface;
use Ilios\CoreBundle\Entity\Manager\PermissionManagerInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

/**
 * Class TopicVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class TopicVoter extends AbstractVoter
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
        return $subject instanceof TopicInterface && in_array($attribute, array(
            self::CREATE, self::VIEW, self::EDIT, self::DELETE
        ));
    }

    /**
     * @param string $attribute
     * @param TopicInterface $topic
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $topic, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            // grant VIEW privileges
            // if the user's primary school is the the topic's owning school
            // - or -
            // if the user has READ rights on the topic's owning school
            // via the permissions system.
            case self::VIEW:
                return (
                    $this->schoolsAreIdentical($topic->getSchool(), $user->getSchool())
                    || $this->permissionManager->userHasReadPermissionToSchool($user, $topic->getSchool())
                );
                break;
            case self::CREATE:
            case self::EDIT:
            case self::DELETE:
                // grant CREATE, EDIT and DELETE privileges
                // if the user has the 'Developer' role
                // - and -
                //   if the user's primary school is the the topic's owning school
                //   - or -
                //   if the user has WRITE rights on the topic's owning school
                // via the permissions system.
                return (
                    $this->userHasRole($user, ['Developer'])
                    && (
                        $this->schoolsAreIdentical($topic->getSchool(), $user->getSchool())
                        || $this->permissionManager->userHasWritePermissionToSchool(
                            $user,
                            $topic->getSchool()
                        )
                    )
                );
                break;
        }

        return false;
    }
}
