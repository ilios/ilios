<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * InstructorGroup
 */
class InstructorGroup
{
    /**
     * @var integer
     */
    private $instructorGroupId;

    /**
     * @var string
     */
    private $title;

    /**
     * @var integer
     */
    private $schoolId;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $groups;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $ilmSessionFacets;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $users;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $offerings;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ilmSessionFacets = new \Doctrine\Common\Collections\ArrayCollection();
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->offerings = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get instructorGroupId
     *
     * @return integer 
     */
    public function getInstructorGroupId()
    {
        return $this->instructorGroupId;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return InstructorGroup
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
     * Set schoolId
     *
     * @param integer $schoolId
     * @return InstructorGroup
     */
    public function setSchoolId($schoolId)
    {
        $this->schoolId = $schoolId;

        return $this;
    }

    /**
     * Get schoolId
     *
     * @return integer 
     */
    public function getSchoolId()
    {
        return $this->schoolId;
    }

    /**
     * Add groups
     *
     * @param \Ilios\CoreBundle\Entity\Group $groups
     * @return InstructorGroup
     */
    public function addGroup(\Ilios\CoreBundle\Entity\Group $groups)
    {
        $this->groups[] = $groups;

        return $this;
    }

    /**
     * Remove groups
     *
     * @param \Ilios\CoreBundle\Entity\Group $groups
     */
    public function removeGroup(\Ilios\CoreBundle\Entity\Group $groups)
    {
        $this->groups->removeElement($groups);
    }

    /**
     * Get groups
     *
     * @return \Ilios\CoreBundle\Entity\Group[]
     */
    public function getGroups()
    {
        return $this->groups->toArray();
    }

    /**
     * Add ilmSessionFacets
     *
     * @param \Ilios\CoreBundle\Entity\IlmSessionFacet $ilmSessionFacets
     * @return InstructorGroup
     */
    public function addIlmSessionFacet(\Ilios\CoreBundle\Entity\IlmSessionFacet $ilmSessionFacets)
    {
        $this->ilmSessionFacets[] = $ilmSessionFacets;

        return $this;
    }

    /**
     * Remove ilmSessionFacets
     *
     * @param \Ilios\CoreBundle\Entity\IlmSessionFacet $ilmSessionFacets
     */
    public function removeIlmSessionFacet(\Ilios\CoreBundle\Entity\IlmSessionFacet $ilmSessionFacets)
    {
        $this->ilmSessionFacets->removeElement($ilmSessionFacets);
    }

    /**
     * Get ilmSessionFacets
     *
     * @return \Ilios\CoreBundle\Entity\IlmSessionFacet[]
     */
    public function getIlmSessionFacets()
    {
        return $this->ilmSessionFacets->toArray();
    }

    /**
     * Add users
     *
     * @param \Ilios\CoreBundle\Entity\User $users
     * @return InstructorGroup
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

    /**
     * Add offerings
     *
     * @param \Ilios\CoreBundle\Entity\Offering $offerings
     * @return InstructorGroup
     */
    public function addOffering(\Ilios\CoreBundle\Entity\Offering $offerings)
    {
        $this->offerings[] = $offerings;

        return $this;
    }

    /**
     * Remove offerings
     *
     * @param \Ilios\CoreBundle\Entity\Offering $offerings
     */
    public function removeOffering(\Ilios\CoreBundle\Entity\Offering $offerings)
    {
        $this->offerings->removeElement($offerings);
    }

    /**
     * Get offerings
     *
     * @return \Ilios\CoreBundle\Entity\Offering[]
     */
    public function getOfferings()
    {
        return $this->offerings->toArray();
    }
}
