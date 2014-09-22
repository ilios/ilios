<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * IlmSessionFacet
 */
class IlmSessionFacet
{
    /**
     * @var integer
     */
    private $ilmSessionFacetId;

    /**
     * @var string
     */
    private $hours;

    /**
     * @var \DateTime
     */
    private $dueDate;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $groups;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $instructorGroups;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $instructors;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $learners;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->instructorGroups = new \Doctrine\Common\Collections\ArrayCollection();
        $this->instructors = new \Doctrine\Common\Collections\ArrayCollection();
        $this->learners = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get ilmSessionFacetId
     *
     * @return integer 
     */
    public function getIlmSessionFacetId()
    {
        return $this->ilmSessionFacetId;
    }

    /**
     * Set hours
     *
     * @param string $hours
     * @return IlmSessionFacet
     */
    public function setHours($hours)
    {
        $this->hours = $hours;

        return $this;
    }

    /**
     * Get hours
     *
     * @return string 
     */
    public function getHours()
    {
        return $this->hours;
    }

    /**
     * Set dueDate
     *
     * @param \DateTime $dueDate
     * @return IlmSessionFacet
     */
    public function setDueDate($dueDate)
    {
        $this->dueDate = $dueDate;

        return $this;
    }

    /**
     * Get dueDate
     *
     * @return \DateTime 
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
     * Add groups
     *
     * @param \Ilios\CoreBundle\Model\Group $groups
     * @return IlmSessionFacet
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
     * Add instructorGroups
     *
     * @param \Ilios\CoreBundle\Model\InstructorGroup $instructorGroups
     * @return IlmSessionFacet
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
     * Add instructors
     *
     * @param \Ilios\CoreBundle\Model\User $instructors
     * @return IlmSessionFacet
     */
    public function addInstructor(\Ilios\CoreBundle\Model\User $instructors)
    {
        $this->instructors[] = $instructors;

        return $this;
    }

    /**
     * Remove instructors
     *
     * @param \Ilios\CoreBundle\Model\User $instructors
     */
    public function removeInstructor(\Ilios\CoreBundle\Model\User $instructors)
    {
        $this->instructors->removeElement($instructors);
    }

    /**
     * Get instructors
     *
     * @return \Ilios\CoreBundle\Model\User[]
     */
    public function getInstructors()
    {
        return $this->instructors->toArray();
    }

    /**
     * Add learners
     *
     * @param \Ilios\CoreBundle\Model\User $learners
     * @return IlmSessionFacet
     */
    public function addLearner(\Ilios\CoreBundle\Model\User $learners)
    {
        $this->learners[] = $learners;

        return $this;
    }

    /**
     * Remove learners
     *
     * @param \Ilios\CoreBundle\Model\User $learners
     */
    public function removeLearner(\Ilios\CoreBundle\Model\User $learners)
    {
        $this->learners->removeElement($learners);
    }

    /**
     * Get learners
     *
     * @return \Ilios\CoreBundle\Model\User[]
     */
    public function getLearners()
    {
        return $this->learners->toArray();
    }
}
