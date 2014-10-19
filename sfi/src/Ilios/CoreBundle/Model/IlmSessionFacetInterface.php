<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Ilios\CoreBundle\Traits\IdentifiableTraitIntertface;

/**
 * Interface IlmSessionFacetInterface
 * @package Ilios\CoreBundle\Model
 */
interface IlmSessionFacetInterface extends IdentifiableTraitIntertface
{
    /**
     * @param string $hours
     */
    public function setHours($hours);

    /**
     * @return string
     */
    public function getHours();

    /**
     * @param \DateTime $dueDate
     */
    public function setDueDate(\DateTime $dueDate);

    /**
     * @return \DateTime
     */
    public function getDueDate();

    /**
     * @param Collection $groups
     */
    public function setGroups(Collection $groups);

    /**
     * @param GroupInterface $groups
     */
    public function addGroup(GroupInterface $groups);

    /**
     * @return ArrayCollection|GroupInterface[]
     */
    public function getGroups();

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
     * @param Collection $learners
     */
    public function setLearners(Collection $learners);

    /**
     * @param UserInterface $learner
     */
    public function addLearner(UserInterface $learner);

    /**
     * @return ArrayCollection|UserInterface[]
     */
    public function getLearners();
}

