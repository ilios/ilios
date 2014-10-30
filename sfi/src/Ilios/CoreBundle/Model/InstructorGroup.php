<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\TitledEntity;

/**
 * Class InstructorGroup
 * @todo: redundant?
 * @package Ilios\CoreBundle\Model
 */
class InstructorGroup implements InstructorGroupInterface
{
    use IdentifiableEntity;
    use TitledEntity;

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
        $this->groups = new ArrayCollection();
        $this->ilmSessionFacets = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->offerings = new ArrayCollection();
    }

    /**
     * @param SchoolInterface $school
     */
    public function setSchool(SchoolInterface $school)
    {
        $this->school = $school;
    }

    /**
     * @return SchoolInterface
     */
    public function getSchool()
    {
        return $this->school;
    }

    /**
     * @param Collection $groups
     */
    public function setGroups(Collection $groups)
    {
        $this->groups = new ArrayCollection();

        foreach ($groups as $group) {
            $this->addGroup($group);
        }
    }

    /**
     * @param GroupInterface $group
     */
    public function addGroup(GroupInterface $group)
    {
        $this->groups->add($group);
    }

    /**
     * @return ArrayCollection|GroupInterface[]
     */
    public function getGroups()
    {
        return $this->groups;
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
        return $this->ilmSessionFacets;
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
     * @param OfferingInterface $offering
     */
    public function addOffering(OfferingInterface $offering)
    {
        $this->offerings->add($offering);
    }

    /**
     * @return ArrayCollection|OfferingInterface[]
     */
    public function getOfferings()
    {
        return $this->offerings;
    }
}
