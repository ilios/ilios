<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Entity\SessionInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class SessionVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class SessionVoter extends CourseVoter
{
    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\SessionInterface');
    }

    /**
     * @param string $attribute
     * @param SessionInterface $session
     * @param UserInterface|null $user
     * @return bool
     */
    protected function isGranted($attribute, $session, $user = null)
    {
        // grant perms based on the owning course
        return parent::isGranted($attribute, $session->getCourse(), $user);
    }

    /**
     * {@inheritdoc}
     */
    protected function isCreateGranted($course, $user)
    {
        return $this->isEditGranted($course, $user);
    }
}
