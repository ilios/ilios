<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Objective
 */
class Objective
{
    /**
     * @var int
     */
    protected $objectiveId;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var \Ilios\CoreBundle\Model\Competency
     */
    protected $competency;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $courses;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $programYears;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $sessions;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $children;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $meshDescriptors;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $parents;

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
     * @return int 
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
     * @param \Ilios\CoreBundle\Model\Competency $competency
     * @return Objective
     */
    public function setCompetency(\Ilios\CoreBundle\Model\Competency $competency = null)
    {
        $this->competency = $competency;

        return $this;
    }

    /**
     * Get competency
     *
     * @return \Ilios\CoreBundle\Model\Competency 
     */
    public function getCompetency()
    {
        return $this->competency;
    }

    /**
     * Get competency id
     *
     * @return int|null
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
     * @param \Ilios\CoreBundle\Model\Course $courses
     * @return Objective
     */
    public function addCourse(\Ilios\CoreBundle\Model\Course $courses)
    {
        $this->courses[] = $courses;

        return $this;
    }

    /**
     * Remove courses
     *
     * @param \Ilios\CoreBundle\Model\Course $courses
     */
    public function removeCourse(\Ilios\CoreBundle\Model\Course $courses)
    {
        $this->courses->removeElement($courses);
    }

    /**
     * Get courses
     *
     * @return \Ilios\CoreBundle\Model\Course[]
     */
    public function getCourses()
    {
        return $this->courses->toArray();
    }

    /**
     * Add programYears
     *
     * @param \Ilios\CoreBundle\Model\ProgramYear $programYears
     * @return Objective
     */
    public function addProgramYear(\Ilios\CoreBundle\Model\ProgramYear $programYears)
    {
        $this->programYears[] = $programYears;

        return $this;
    }

    /**
     * Remove programYears
     *
     * @param \Ilios\CoreBundle\Model\ProgramYear $programYears
     */
    public function removeProgramYear(\Ilios\CoreBundle\Model\ProgramYear $programYears)
    {
        $this->programYears->removeElement($programYears);
    }

    /**
     * Get programYears
     *
     * @return \Ilios\CoreBundle\Model\ProgramYear[]
     */
    public function getProgramYears()
    {
        return $this->programYears->toArray();
    }

    /**
     * Add sessions
     *
     * @param \Ilios\CoreBundle\Model\Session $sessions
     * @return Objective
     */
    public function addSession(\Ilios\CoreBundle\Model\Session $sessions)
    {
        $this->sessions[] = $sessions;

        return $this;
    }

    /**
     * Remove sessions
     *
     * @param \Ilios\CoreBundle\Model\Session $sessions
     */
    public function removeSession(\Ilios\CoreBundle\Model\Session $sessions)
    {
        $this->sessions->removeElement($sessions);
    }

    /**
     * Get sessions
     *
     * @return \Ilios\CoreBundle\Model\Session[]
     */
    public function getSessions()
    {
        return $this->sessions->toArray();
    }

    /**
     * Add children
     *
     * @param \Ilios\CoreBundle\Model\Objective $children
     * @return Objective
     */
    public function addChild(\Ilios\CoreBundle\Model\Objective $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \Ilios\CoreBundle\Model\Objective $children
     */
    public function removeChild(\Ilios\CoreBundle\Model\Objective $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Ilios\CoreBundle\Model\Objective[]
     */
    public function getChildren()
    {
        return $this->children->toArray();
    }

    /**
     * Add meshDescriptors
     *
     * @param \Ilios\CoreBundle\Model\MeshDescriptor $meshDescriptors
     * @return Objective
     */
    public function addMeshDescriptor(\Ilios\CoreBundle\Model\MeshDescriptor $meshDescriptors)
    {
        $this->meshDescriptors[] = $meshDescriptors;

        return $this;
    }

    /**
     * Remove meshDescriptors
     *
     * @param \Ilios\CoreBundle\Model\MeshDescriptor $meshDescriptors
     */
    public function removeMeshDescriptor(\Ilios\CoreBundle\Model\MeshDescriptor $meshDescriptors)
    {
        $this->meshDescriptors->removeElement($meshDescriptors);
    }

    /**
     * Get meshDescriptors
     *
     * @return \Ilios\CoreBundle\Model\MeshDescriptor[]
     */
    public function getMeshDescriptors()
    {
        return $this->meshDescriptors->toArray();
    }

    /**
     * Add parents
     *
     * @param \Ilios\CoreBundle\Model\Objective $parents
     * @return Objective
     */
    public function addParent(\Ilios\CoreBundle\Model\Objective $parents)
    {
        $this->parents[] = $parents;

        return $this;
    }

    /**
     * Remove parents
     *
     * @param \Ilios\CoreBundle\Model\Objective $parents
     */
    public function removeParent(\Ilios\CoreBundle\Model\Objective $parents)
    {
        $this->parents->removeElement($parents);
    }

    /**
     * Get parents
     *
     * @return \Ilios\CoreBundle\Model\Objective[]
     */
    public function getParents()
    {
        return $this->parents->toArray();
    }
}
