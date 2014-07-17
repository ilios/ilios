<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Objective
 */
class Objective
{
    /**
     * @var integer
     */
    private $objectiveId;

    /**
     * @var string
     */
    private $title;

    /**
     * @var \Ilios\CoreBundle\Entity\Competency
     */
    private $competency;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $courses;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $programYears;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $sessions;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $children;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $meshDescriptors;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $parents;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->courses = new \Doctrine\Common\Collections\ArrayCollection();
        $this->programYears = new \Doctrine\Common\Collections\ArrayCollection();
        $this->sessions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->children = new \Doctrine\Common\Collections\ArrayCollection();
        $this->meshDescriptors = new \Doctrine\Common\Collections\ArrayCollection();
        $this->parents = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get objectiveId
     *
     * @return integer 
     */
    public function getObjectiveId()
    {
        return $this->objectiveId;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Objective
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
     * Set competency
     *
     * @param \Ilios\CoreBundle\Entity\Competency $competency
     * @return Objective
     */
    public function setCompetency(\Ilios\CoreBundle\Entity\Competency $competency = null)
    {
        $this->competency = $competency;

        return $this;
    }

    /**
     * Get competency
     *
     * @return \Ilios\CoreBundle\Entity\Competency 
     */
    public function getCompetency()
    {
        return $this->competency;
    }

    /**
     * Get competency id
     *
     * @return integer|null
     */
    public function getCompetencyId()
    {
        if ($this->competency) {
            return $this->competency->getCompetencyId();
        }

        return null;
    }

    /**
     * Add courses
     *
     * @param \Ilios\CoreBundle\Entity\Course $courses
     * @return Objective
     */
    public function addCourse(\Ilios\CoreBundle\Entity\Course $courses)
    {
        $this->courses[] = $courses;

        return $this;
    }

    /**
     * Remove courses
     *
     * @param \Ilios\CoreBundle\Entity\Course $courses
     */
    public function removeCourse(\Ilios\CoreBundle\Entity\Course $courses)
    {
        $this->courses->removeElement($courses);
    }

    /**
     * Get courses
     *
     * @return \Ilios\CoreBundle\Entity\Course[]
     */
    public function getCourses()
    {
        return $this->courses->toArray();
    }

    /**
     * Add programYears
     *
     * @param \Ilios\CoreBundle\Entity\ProgramYear $programYears
     * @return Objective
     */
    public function addProgramYear(\Ilios\CoreBundle\Entity\ProgramYear $programYears)
    {
        $this->programYears[] = $programYears;

        return $this;
    }

    /**
     * Remove programYears
     *
     * @param \Ilios\CoreBundle\Entity\ProgramYear $programYears
     */
    public function removeProgramYear(\Ilios\CoreBundle\Entity\ProgramYear $programYears)
    {
        $this->programYears->removeElement($programYears);
    }

    /**
     * Get programYears
     *
     * @return \Ilios\CoreBundle\Entity\ProgramYear[]
     */
    public function getProgramYears()
    {
        return $this->programYears->toArray();
    }

    /**
     * Add sessions
     *
     * @param \Ilios\CoreBundle\Entity\Session $sessions
     * @return Objective
     */
    public function addSession(\Ilios\CoreBundle\Entity\Session $sessions)
    {
        $this->sessions[] = $sessions;

        return $this;
    }

    /**
     * Remove sessions
     *
     * @param \Ilios\CoreBundle\Entity\Session $sessions
     */
    public function removeSession(\Ilios\CoreBundle\Entity\Session $sessions)
    {
        $this->sessions->removeElement($sessions);
    }

    /**
     * Get sessions
     *
     * @return \Ilios\CoreBundle\Entity\Session[]
     */
    public function getSessions()
    {
        return $this->sessions->toArray();
    }

    /**
     * Add children
     *
     * @param \Ilios\CoreBundle\Entity\Objective $children
     * @return Objective
     */
    public function addChild(\Ilios\CoreBundle\Entity\Objective $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Ilios\CoreBundle\Entity\Objective $children
     */
    public function removeChild(\Ilios\CoreBundle\Entity\Objective $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Ilios\CoreBundle\Entity\Objective[]
     */
    public function getChildren()
    {
        return $this->children->toArray();
    }

    /**
     * Add meshDescriptors
     *
     * @param \Ilios\CoreBundle\Entity\MeshDescriptor $meshDescriptors
     * @return Objective
     */
    public function addMeshDescriptor(\Ilios\CoreBundle\Entity\MeshDescriptor $meshDescriptors)
    {
        $this->meshDescriptors[] = $meshDescriptors;

        return $this;
    }

    /**
     * Remove meshDescriptors
     *
     * @param \Ilios\CoreBundle\Entity\MeshDescriptor $meshDescriptors
     */
    public function removeMeshDescriptor(\Ilios\CoreBundle\Entity\MeshDescriptor $meshDescriptors)
    {
        $this->meshDescriptors->removeElement($meshDescriptors);
    }

    /**
     * Get meshDescriptors
     *
     * @return \Ilios\CoreBundle\Entity\MeshDescriptor[]
     */
    public function getMeshDescriptors()
    {
        return $this->meshDescriptors->toArray();
    }

    /**
     * Add parents
     *
     * @param \Ilios\CoreBundle\Entity\Objective $parents
     * @return Objective
     */
    public function addParent(\Ilios\CoreBundle\Entity\Objective $parents)
    {
        $this->parents[] = $parents;

        return $this;
    }

    /**
     * Remove parents
     *
     * @param \Ilios\CoreBundle\Entity\Objective $parents
     */
    public function removeParent(\Ilios\CoreBundle\Entity\Objective $parents)
    {
        $this->parents->removeElement($parents);
    }

    /**
     * Get parents
     *
     * @return \Ilios\CoreBundle\Entity\Objective[]
     */
    public function getParents()
    {
        return $this->parents->toArray();
    }
}
