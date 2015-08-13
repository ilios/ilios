<?php

namespace Ilios\AuthenticationBundle\Voter;

use Ilios\CoreBundle\Classes\TemporaryFileSystem;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Class TemporaryFileSystemVoter
 * @package Ilios\AuthenticationBundle\Voter
 */
class TemporaryFileSystemVoter extends AbstractVoter
{
    /**
     * {@inheritdoc}
     */
    public function getSupportedAttributes()
    {
        return array(self::CREATE);
    }

    /**
     * {@inheritdoc}
     */
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Classes\TemporaryFileSystem');
    }

    /**
     * @param string $attribute
     * @param TemporaryFileSystem $fileSystem
     * @param UserInterface $user
     * @return bool
     */
    protected function isGranted($attribute, $fileSystem, $user = null)
    {
        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            // only user with Faculty/Course Director/Developer roles
            // have CREATE permissions to the temporary file system.
            case self::CREATE:
                return ($this->userHasRole($user, ['Faculty', 'Course Director','Developer']));
                break;
        }

        return false;
    }
}
