<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\IdentifiableTrait;
use Ilios\CoreBundle\Traits\TitleTrait;

/**
 * @TODO: Ask about group table & relationship to this... Seems to break NF.
 * Class InstructorGroup
 * @package Ilios\CoreBundle\Model
 */
class InstructorGroup
{
    use IdentifiableTrait;
    use TitleTrait;

    /**
     * @var SchoolInterface
     */
    protected $school;

    /**
     * @var ArrayCollection|GroupInterface[]
     */
    protected $groups;

    /**
     * @var ArrayCollection|IlmSessionFacet[]
     */
    protected $ilmSessionFacets;

    /**
     * @var ArrayCollection|UserInterface[]
     */
    protected $users;

    /**
     * @var ArrayCollection|OfferingInterface[]
     */
    protected $offerings;

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
     * @param \Ilios\CoreBundle\Model\Group $groups
     * @return InstructorGroup
     */
    public function addGroup(\Ilios\CoreBundle\Model\Group $groups)
    {
        $this->groups[] = $groups;

        return $this;
    }

    /**
     * Remove groups
     *
     * @param \Ilios\CoreBundle\Model\Group $groups
     */
    public function removeGroup(\Ilios\CoreBundle\Model\Group $groups)
    {
        $this->groups->removeElement($groups);
    }

    /**
     * Get groups
     *
     * @return \Ilios\CoreBundle\Model\Group[]
     */
    public function getGroups()
    {
        return $this->groups->toArray();
    }

    /**
     * Add ilmSessionFacets
     *
     * @param \Ilios\CoreBundle\Model\IlmSessionFacet $ilmSessionFacets
     * @return InstructorGroup
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
     * Add users
     *
     * @param \Ilios\CoreBundle\Model\User $users
     * @return InstructorGroup
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
     * Add offerings
     *
     * @param \Ilios\CoreBundle\Model\Offering $offerings
     * @return InstructorGroup
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
}
