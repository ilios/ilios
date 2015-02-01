<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;

/**
 * Interface GroupInterface
 * @package Ilios\CoreBundle\Entity
 */
interface GroupInterface extends IdentifiableEntityInterface, TitledEntityInterface
{
    /**
     * @param string $location
     */
    public function setLocation($location);

    /**
     * @return string
     */
    public function getLocation();

    /**
     * @param CohortInterface $cohort
     */
    public function setCohort(CohortInterface $cohort);

    /**
     * @return CohortInterface
     */
    public function getCohort();

    /**
     * @param string $instructors
     */
    public function setInstructors($instructors);

    /**
     * @return string
     */
    public function getInstructors();

    /**
     * @param Collection $users
     */
    public function setUsers(Collection $users);

    /**
     * @param UserInterface $user
     */
    public function addUser(UserInterface $user);

    /**
     * @return ArrayCollection|UserInterface[]
     */
    public function getUsers();

    /**
     * @param Collection $ilmSessionFacets
     */
    public function setIlmSessionFacets(Collection $ilmSessionFacets);

    /**
     * @param IlmSessionFacetInterface $ilmSessionFacet
     */
    public function addIlmSessionFacet(IlmSessionFacetInterface $ilmSessionFacet);

    /**
     * @return ArrayCollection|IlmSessionFacetInterface[]
     */
    public function getIlmSessionFacets();

    /**
     * @param Collection $offerings
     */
    public function setOfferings(Collection $offerings);

    /**
     * @param OfferingInterface $offering
     */
    public function addOffering(OfferingInterface $offering);

    /**
     * @return ArrayCollection|OfferingInterface[]
     */
    public function getOfferings();

    /**
     * @param GroupInterface $parent
     */
    public function setParent(GroupInterface $parent);

    /**
     * @return GroupInterface
     */
    public function getParent();

    /**
     * @param Collection $children
     */
    public function setChildren(Collection $children);

    /**
     * @param GroupInterface $child
     */
    public function addChild(GroupInterface $child);

    /**
     * @return ArrayCollection|GroupInterface[]
     */
    public function getChildren();

    /**
     * @param Collection $instructorGroups
     */
    public function setInstructorGroups(Collection $instructorGroups);

    /**
     * @param InstructorGroupInterface $instructorGroup
     */
    public function addInstructorGroup(InstructorGroupInterface $instructorGroup);

    /**
     * @return ArrayCollection|InstructorGroupInterface[]
     */
    public function getInstructorGroups();

    /**
     * @param Collection $instructorUsers
     */
    public function setInstructorUsers(Collection $instructorUsers);

    /**
     * @param UserInterface $instructorUser
     */
    public function addInstructorUser(UserInterface $instructorUser);

    /**
     * @return ArrayCollection|UserInterface[]
     */
    public function getInstructorUsers();
}

