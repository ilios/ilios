<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\TitledEntityInterface;

/**
 * Interface InstructorGroupInterface
 * @package Ilios\CoreBundle\Entity
 */
interface InstructorGroupInterface extends IdentifiableEntityInterface, TitledEntityInterface
{
    /**
     * @param SchoolInterface $school
     */
    public function setSchool(SchoolInterface $school);

    /**
     * @return SchoolInterface
     */
    public function getSchool();

    /**
     * @param Collection $groups
     */
    public function setGroups(Collection $groups);

    /**
     * @param GroupInterface $group
     */
    public function addGroup(GroupInterface $group);

    /**
     * @return ArrayCollection|GroupInterface[]
     */
    public function getGroups();

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
}

