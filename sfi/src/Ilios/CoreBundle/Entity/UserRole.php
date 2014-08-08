<?php

namespace Ilios\CoreBundle\Entity;

use Symfony\Component\Security\Core\Role\RoleInterface;

/**
 * UserRole
 */
class UserRole implements RoleInterface
{
    /**
     * @var integer
     */
    private $userRoleId;

    /**
     * @var string
     */
    private $title;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $users;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get userRoleId
     *
     * @return integer 
     */
    public function getUserRoleId()
    {
        return $this->userRoleId;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return UserRole
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Add users
     *
     * @param \Ilios\CoreBundle\Entity\User $users
     * @return UserRole
     */
    public function addUser(\Ilios\CoreBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \Ilios\CoreBundle\Entity\User $users
     */
    public function removeUser(\Ilios\CoreBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Ilios\CoreBundle\Entity\User[]
     */
    public function getUsers()
    {
        return $this->users->toArray();
    }

    public function getRole()
    {
        return 'ROLE_' . $this->title;
    }
}
