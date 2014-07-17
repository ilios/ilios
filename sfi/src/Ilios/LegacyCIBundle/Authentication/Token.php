<?php
namespace Ilios\LegacyCIBundle\Authentication;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Role\RoleInterface;

use Ilios\LegacyCIBundle\Session\Handler;

class Token extends AbstractToken
{
    private $roles;
    
    /**
     * @param Handler $handler
     */
    public function __construct(Handler $handler)
    {
        //construct the parent with an empty roles array
        parent::__construct();
        $this->roles = array();
        $userId = $handler->getUserId();
        if ($userId) {
            $this->setUser($userId);
            $this->setAuthenticated(true);
        }
    }
    
    /**
     * Get roles from the user interface if it is set
     * {@inheritdoc}
     */
    public function setUser($user)
    {
        parent::setUser($user);
        if ($user instanceof UserInterface) {
            $this->roles = array();
            foreach ($user->getRoles() as $role) {
                if (is_string($role)) {
                    $role = new Role($role);
                } elseif (!$role instanceof RoleInterface) {
                    throw new \InvalidArgumentException(
                        sprintf(
                            '$roles must be an array of strings, or RoleInterface instances, but got %s.',
                            gettype($role)
                        )
                    );
                }

                $this->roles[] = $role;
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials()
    {
        return '';
    }
}
