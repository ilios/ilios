<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Group
 */
class Group
{
    /**
     * @var integer
     */
    private $groupId;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $instructors;

    /**
     * @var string
     */
    private $location;

    /**
     * @var \Ilios\CoreBundle\Model\Cohort
     */
    private $cohort;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $users;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $instructorUsers;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $instructorGroups;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $ilmSessionFacets;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $offerings;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $parents;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
        $this->instructorUsers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->instructorGroups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->ilmSessionFacets = new \Doctrine\Common\Collections\ArrayCollection();
        $this->offerings = new \Doctrine\Common\Collections\ArrayCollection();
        $this->parents = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get groupId
     *
     * @return integer 
     */
    public function getGroupId()
    {
        return $this->groupId;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Group
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
     * Set instructors
     *
     * @param string $instructors
     * @return Group
     */
    public function setInstructors($instructors)
    {
        $this->instructors = $instructors;

        return $this;
    }

    /**
     * Get instructors
     *
     * @return string 
     */
    public function getInstructors()
    {
        return $this->instructors;
    }

    /**
     * Set location
     *
     * @param string $location
     * @return Group
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * Get location
     *
     * @return string 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * Set cohort
     *
     * @param \Ilios\CoreBundle\Model\Cohort $cohort
     * @return Group
     */
    public function setCohort(\Ilios\CoreBundle\Model\Cohort $cohort = null)
    {
        $this->cohort = $cohort;

        return $this;
    }

    /**
     * Get cohort
     *
     * @return \Ilios\CoreBundle\Model\Cohort 
     */
    public function getCohort()
    {
        return $this->cohort;
    }

    /**
     * Add users
     *
     * @param \Ilios\CoreBundle\Model\User $users
     * @return Group
     */
    public function addUser(\Ilios\CoreBundle\Model\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \Ilios\CoreBundle\Model\User $users
     */
    public function removeUser(\Ilios\CoreBundle\Model\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Ilios\CoreBundle\Model\User[]
     */
    public function getUsers()
    {
        return $this->users->toArray();
    }

    /**
     * Add instructorUsers
     *
     * @param \Ilios\CoreBundle\Model\User $instructorUsers
     * @return Group
     */
    public function addInstructorUser(\Ilios\CoreBundle\Model\User $instructorUsers)
    {
        $this->instructorUsers[] = $instructorUsers;

        return $this;
    }

    /**
     * Remove instructorUsers
     *
     * @param \Ilios\CoreBundle\Model\User $instructorUsers
     */
    public function removeInstructorUser(\Ilios\CoreBundle\Model\User $instructorUsers)
    {
        $this->instructorUsers->removeElement($instructorUsers);
    }

    /**
     * Get instructorUsers
     *
     * @return \Ilios\CoreBundle\Model\User[]
     */
    public function getInstructorUsers()
    {
        return $this->instructorUsers->toArray();
    }

    /**
     * Add instructorGroups
     *
     * @param \Ilios\CoreBundle\Model\InstructorGroup $instructorGroups
     * @return Group
     */
    public function addInstructorGroup(\Ilios\CoreBundle\Model\InstructorGroup $instructorGroups)
    {
        $this->instructorGroups[] = $instructorGroups;

        return $this;
    }

    /**
     * Remove instructorGroups
     *
     * @param \Ilios\CoreBundle\Model\InstructorGroup $instructorGroups
     */
    public function removeInstructorGroup(\Ilios\CoreBundle\Model\InstructorGroup $instructorGroups)
    {
        $this->instructorGroups->removeElement($instructorGroups);
    }

    /**
     * Get instructorGroups
     *
     * @return \Ilios\CoreBundle\Model\InstructorGroup[]
     */
    public function getInstructorGroups()
    {
        return $this->instructorGroups->toArray();
    }

    /**
     * Add ilmSessionFacets
     *
     * @param \Ilios\CoreBundle\Model\IlmSessionFacet $ilmSessionFacets
     * @return Group
     */
    public function addIlmSessionFacet(\Ilios\CoreBundle\Model\IlmSessionFacet $ilmSessionFacets)
    {
        $this->ilmSessionFacets[] = $ilmSessionFacets;

        return $this;
    }

    /**
     * Remove ilmSessionFacets
     *
     * @param \Ilios\CoreBundle\Model\IlmSessionFacet $ilmSessionFacets
     */
    public function removeIlmSessionFacet(\Ilios\CoreBundle\Model\IlmSessionFacet $ilmSessionFacets)
    {
        $this->ilmSessionFacets->removeElement($ilmSessionFacets);
    }

    /**
     * Get ilmSessionFacets
     *
     * @return \Ilios\CoreBundle\Model\IlmSessionFacet[]
     */
    public function getIlmSessionFacets()
    {
        return $this->ilmSessionFacets->toArray();
    }

    /**
     * Add offerings
     *
     * @param \Ilios\CoreBundle\Model\Offering $offerings
     * @return Group
     */
    public function addOffering(\Ilios\CoreBundle\Model\Offering $offerings)
    {
        $this->offerings[] = $offerings;

        return $this;
    }

    /**
     * Remove offerings
     *
     * @param \Ilios\CoreBundle\Model\Offering $offerings
     */
    public function removeOffering(\Ilios\CoreBundle\Model\Offering $offerings)
    {
        $this->offerings->removeElement($offerings);
    }

    /**
     * Get offerings
     *
     * @return \Ilios\CoreBundle\Model\Offering[]
     */
    public function getOfferings()
    {
        return $this->offerings->toArray();
    }

    /**
     * Add parent
     *
     * @param \Ilios\CoreBundle\Model\Group $parent
     * @return Group
     */
    public function addParent(\Ilios\CoreBundle\Model\Group $parent)
    {
        $this->parents[] = $parent;

        return $this;
    }

    /**
     * Remove parent
     *
     * @param \Ilios\CoreBundle\Model\Group $parent
     */
    public function removeParent(\Ilios\CoreBundle\Model\Group $parent)
    {
        $this->parents->removeElement($parent);
    }

    /**
     * Get parents
     *
     * @return \Ilios\CoreBundle\Model\Group[]
     */
    public function getParents()
    {
        return $this->parents->toArray();
    }
}
