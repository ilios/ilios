<?php

namespace Ilios\CoreBundle\Entity;

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
     * @param \Ilios\CoreBundle\Entity\Group $groups
     * @return IlmSessionFacet
     */
    public function addGroup(\Ilios\CoreBundle\Entity\Group $groups)
    {
        $this->groups[] = $groups;

        return $this;
    }

    /**
     * Remove groups
     *
     * @param \Ilios\CoreBundle\Entity\Group $groups
     */
    public function removeGroup(\Ilios\CoreBundle\Entity\Group $groups)
    {
        $this->groups->removeElement($groups);
    }

    /**
     * Get groups
     *
     * @return \Ilios\CoreBundle\Entity\Group[]
     */
    public function getGroups()
    {
        return $this->groups->toArray();
    }

    /**
     * Add instructorGroups
     *
     * @param \Ilios\CoreBundle\Entity\InstructorGroup $instructorGroups
     * @return IlmSessionFacet
     */
    public function addInstructorGroup(\Ilios\CoreBundle\Entity\InstructorGroup $instructorGroups)
    {
        $this->instructorGroups[] = $instructorGroups;

        return $this;
    }

    /**
     * Remove instructorGroups
     *
     * @param \Ilios\CoreBundle\Entity\InstructorGroup $instructorGroups
     */
    public function removeInstructorGroup(\Ilios\CoreBundle\Entity\InstructorGroup $instructorGroups)
    {
        $this->instructorGroups->removeElement($instructorGroups);
    }

    /**
     * Get instructorGroups
     *
     * @return \Ilios\CoreBundle\Entity\InstructorGroup[]
     */
    public function getInstructorGroups()
    {
        return $this->instructorGroups->toArray();
    }

    /**
     * Add instructors
     *
     * @param \Ilios\CoreBundle\Entity\User $instructors
     * @return IlmSessionFacet
     */
    public function addInstructor(\Ilios\CoreBundle\Entity\User $instructors)
    {
        $this->instructors[] = $instructors;

        return $this;
    }

    /**
     * Remove instructors
     *
     * @param \Ilios\CoreBundle\Entity\User $instructors
     */
    public function removeInstructor(\Ilios\CoreBundle\Entity\User $instructors)
    {
        $this->instructors->removeElement($instructors);
    }

    /**
     * Get instructors
     *
     * @return \Ilios\CoreBundle\Entity\User[]
     */
    public function getInstructors()
    {
        return $this->instructors->toArray();
    }

    /**
     * Add learners
     *
     * @param \Ilios\CoreBundle\Entity\User $learners
     * @return IlmSessionFacet
     */
    public function addLearner(\Ilios\CoreBundle\Entity\User $learners)
    {
        $this->learners[] = $learners;

        return $this;
    }

    /**
     * Remove learners
     *
     * @param \Ilios\CoreBundle\Entity\User $learners
     */
    public function removeLearner(\Ilios\CoreBundle\Entity\User $learners)
    {
        $this->learners->removeElement($learners);
    }

    /**
     * Get learners
     *
     * @return \Ilios\CoreBundle\Entity\User[]
     */
    public function getLearners()
    {
        return $this->learners->toArray();
    }
}
