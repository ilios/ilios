<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Ilios\CoreBundle\Traits\IdentifiableTrait;

/**
 * Class IlmSessionFacet
 * @package Ilios\CoreBundle\Model
 */
class IlmSessionFacet implements IlmSessionFacetInterface
{
    use IdentifiableTrait;

    /**
     * @var string
     */
    protected $hours;

    /**
     * @var \DateTime
     */
    protected $dueDate;

    /**
     * @var ArrayCollection|GroupInterface[]
     */
    protected $groups;

    /**
     * @var ArrayCollection|UserInterface[]
     */
    protected $instructors;

    /**
     * @var ArrayCollection|InstructorGroupInterface[]
     */
    protected $instructorGroups;

    /**
     * @var ArrayCollection|UserInterface[]
     */
    protected $learners;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->instructors = new ArrayCollection();
        $this->instructorGroups = new ArrayCollection();
        $this->learners = new ArrayCollection();
    }

    /**
     * @param string $hours
     */
    public function setHours($hours)
    {
        $this->hours = $hours;
    }

    /**
     * @return string
     */
    public function getHours()
    {
        return $this->hours;
    }

    /**
     * @param \DateTime $dueDate
     */
    public function setDueDate(\DateTime $dueDate)
    {
        $this->dueDate = $dueDate;
    }

    /**
     * @return \DateTime
     */
    public function getDueDate()
    {
        return $this->dueDate;
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
     * @param GroupInterface $groups
     */
    public function addGroup(GroupInterface $groups)
    {
        $this->groups->add($groups);
    }

    /**
     * @return ArrayCollection|GroupInterface[]
     */
    public function getGroups()
    {
        return $this->groups;
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

    /**
     * @param Collection $learners
     */
    public function setLearners(Collection $learners)
    {
        $this->learners = new ArrayCollection();

        foreach ($learners as $learner) {
            $this->addLearner($learner);
        }
    }

    /**
     * @param UserInterface $learner
     */
    public function addLearner(UserInterface $learner)
    {
        $this->learners->add($learner);
    }

    /**
     * @return ArrayCollection|UserInterface[]
     */
    public function getLearners()
    {
        return $this->learners;
    }
}
