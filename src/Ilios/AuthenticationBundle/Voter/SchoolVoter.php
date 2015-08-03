<?php

namespace Ilios\AuthenticationBundle\Voter;

use Symfony\Component\Security\Core\Authorization\Voter\AbstractVoter;
use Symfony\Component\Security\Core\User\UserInterface;

class SchoolVoter extends AbstractVoter
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';
    
    protected function getSupportedAttributes()
    {
        return array(self::VIEW, self::EDIT, self::DELETE);
    }
    
    protected function getSupportedClasses()
    {
        return array('Ilios\CoreBundle\Entity\School');
    }
    
    protected function isGranted($attribute, $school, $user = null)
    {
        // make sure there is a user object (i.e. that the user is logged in)
        if (!$user instanceof UserInterface) {
            return false;
        }
        
        switch ($attribute) {
            case self::VIEW:
                if ($school->getId() === $user->getPrimarySchool()->getId()) {
                    return true;
                }
            
                break;
            case self::EDIT:
            case self::DELETE:
                if ($school->getId() === $user->getPrimarySchool()->getId()) {
                    $roles = array_map(function ($role) {
                        return $role->getTitle();
                    }, $user->getRoles()->toArray());
                    $eligibleRoles = ['Course Director', 'Developer', 'Faculty'];
                    return array_intersect($eligibleRoles, $roles);
                }

                break;
            
            return false;
        }
    }
}
