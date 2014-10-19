<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Ilios\CoreBundle\Traits\IdentifiableTraitIntertface;
use Ilios\CoreBundle\Traits\TitleTraitInterface;

/**
 * Interface GroupInterface
 * @package Ilios\CoreBundle\Model
 */
interface GroupInterface extends IdentifiableTraitIntertface, TitleTraitInterface
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
     * @param Collection $instructors
     */
    public function setInstructors(Collection $instructors);

    /**
     * @param UserInterface $instructor
     */
    public function addInstructor(UserInterface $instructor);

    /**
     * @return ArrayCollection|UserInterface[]
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
     * @param OfferingInterface $offerings
     */
    public function addOffering(OfferingInterface $offerings);

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
}

