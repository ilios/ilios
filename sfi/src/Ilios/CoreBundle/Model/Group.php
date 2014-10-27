<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\IdentifiableTrait;
use Ilios\CoreBundle\Traits\TitleTrait;

/**
 * @TODO: Ask about instructor_group table & relationship to this... Seems to break NF.
 * Class Group
 * @package Ilios\CoreBundle\Model
 */
class Group implements GroupInterface
{
    use IdentifiableTrait;
    use TitleTrait;

    /**
     * @var string
     */
    protected $location;

    /**
     * @var CohortInterface
     */
    protected $cohort;

    /**
     * @var GroupInterface
     */
    protected $parent;

    /**
     * @var ArrayCollection|GroupInterface[]
     */
    protected $children;

    /**
     * @var ArrayCollection|IlmSessionFacetInterface[]
     */
    protected $ilmSessionFacets;

    /**
     * @var ArrayCollection|OfferingInterface[]
     */
    protected $offerings;

    /**
     * @var ArrayCollection|UserGroupInterface[]
     */
    protected $instructors;

    /**
     * @todo: Redundant?
     * @var ArrayCollection|InstructorGroupInterface[]
     */
    protected $instructorGroups;

    /**
     * @var ArrayCollection|UserGroupInterface[]
     */
    protected $users;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->instructors = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->ilmSessionFacets = new ArrayCollection();
        $this->offerings = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->instructorGroups = new ArrayCollection();
    }

    /**
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param CohortInterface $cohort
     */
    public function setCohort(CohortInterface $cohort)
    {
        $this->cohort = $cohort;
    }

    /**
     * @return CohortInterface
     */
    public function getCohort()
    {
        return $this->cohort;
    }

    /**
     * @param Collection $instructors
     */
    public function setInstructors(Collection $instructors)
    {
        $this->instructors = new ArrayCollection();

        foreach ($instructors as $instructor) {
            $this->addInstructor($instructor);
        }
    }

    /**
     * @param UserInterface $instructor
     */
    public function addInstructor(UserInterface $instructor)
    {
        $this->instructors->add($instructor);
    }

    /**
     * @return ArrayCollection|UserInterface[]
     */
    public function getInstructors()
    {
        return $this->instructors;
    }

    /**
     * @param Collection $users
     */
    public function setUsers(Collection $users)
    {
        $this->users = new ArrayCollection();

        foreach ($users as $user) {
            $this->addUser($user);
        }
    }

    /**
     * @param UserInterface $user
     */
    public function addUser(UserInterface $user)
    {
        $this->users->add($user);
    }

    /**
     * @return ArrayCollection|UserInterface[]
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param Collection $ilmSessionFacets
     */
    public function setIlmSessionFacets(Collection $ilmSessionFacets)
    {
        $this->ilmSessionFacets = new ArrayCollection();

        foreach ($ilmSessionFacets as $ilmSessionFacet) {
            $this->addIlmSessionFacet($ilmSessionFacet);
        }
    }

    /**
     * @param IlmSessionFacetInterface $ilmSessionFacet
     */
    public function addIlmSessionFacet(IlmSessionFacetInterface $ilmSessionFacet)
    {
        $this->ilmSessionFacets->add($ilmSessionFacet);
    }

    /**
     * @return ArrayCollection|IlmSessionFacetInterface[]
     */
    public function getIlmSessionFacets()
    {
        return $this->ilmSessionFacets->toArray();
    }

    /**
     * @param Collection $offerings
     */
    public function setOfferings(Collection $offerings)
    {
        $this->offerings = new ArrayCollection();

        foreach ($offerings as $offering) {
            $this->addOffering($offering);
        }
    }

    /**
     * @param OfferingInterface $offerings
     */
    public function addOffering(OfferingInterface $offerings)
    {
        $this->offerings->add($offerings);
    }

    /**
     * @return ArrayCollection|OfferingInterface[]
     */
    public function getOfferings()
    {
        return $this->offerings;
    }

    /**
     * @param GroupInterface $parent
     */
    public function setParent(GroupInterface $parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return GroupInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Collection $children
     */
    public function setChildren(Collection $children)
    {
        $this->children = new ArrayCollection();

        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    /**
     * @param GroupInterface $child
     */
    public function addChild(GroupInterface $child)
    {
        $this->children->add($child);
    }

    /**
     * @return ArrayCollection|GroupInterface[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param Collection $instructorGroups
     */
    public function setInstructorGroups(Collection $instructorGroups)
    {
        $this->instructorGroups = new ArrayCollection();

        foreach ($instructorGroups as $instructorGroup) {
            $this->addInstructorGroup($instructorGroup);
        }
    }

    /**
     * @param InstructorGroupInterface $instructorGroup
     */
    public function addInstructorGroup(InstructorGroupInterface $instructorGroup)
    {
        $this->instructorGroups->add($instructorGroup);
    }

    /**
     * @return ArrayCollection|InstructorGroupInterface[]
     */
    public function getInstructorGroups()
    {
        return $this->instructorGroups;
    }
}
