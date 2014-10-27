<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Traits\IdentifiableTraitInterface;
use Ilios\CoreBundle\Traits\TitleTraitInterface;

/**
 * Interface CourseInterface
 */
interface CourseInterface extends IdentifiableTraitInterface, TitleTraitInterface
{
    /**
     * @param integer $level
     */
    public function setLevel($level);

    /**
     * @return integer
     */
    public function getLevel();

    /**
     * @param integer $year
     */
    public function setYear($year);

    /**
     * @return integer
     */
    public function getYear();

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate(\DateTime $startDate);

    /**
     * @return \DateTime
     */
    public function getStartDate();

    /**
     * @param \DateTime $endDate
     */
    public function setEndDate(\DateTime $endDate);

    /**
     * @return \DateTime
     */
    public function getEndDate();

    /**
     * @param boolean $deleted
     */
    public function setDeleted($deleted);

    /**
     * @return boolean
     */
    public function isDeleted();

    /**
     * @todo: Possible rename.
     * @param string $externalName
     */
    public function setExternalName($externalName);

    /**
     * @todo: Possible rename.
     * @return string
     */
    public function getExternalName();

    /**
     * @param boolean $locked
     */
    public function setLocked($locked);

    /**
     * @return boolean
     */
    public function isLocked();

    /**
     * @param boolean $archived
     */
    public function setArchived($archived);

    /**
     * @return boolean
     */
    public function isArchived();

    /**
     * @param boolean $publishedAsTbd
     */
    public function setPublishedAsTbd($publishedAsTbd);

    /**
     * @return boolean
     */
    public function isPublishedAsTbd();

    /**
     * @param CourseClerkshipTypeInterface $clerkshipType
     */
    public function setClerkshipType(CourseClerkshipTypeInterface $clerkshipType);

    /**
     * @return \Ilios\CoreBundle\Model\CourseClerkshipType
     */
    public function getClerkshipType();

    /**
     * @param SchoolInterface $school
     */
    public function setSchool(SchoolInterface $school);

    /**
     * @return SchoolInterface
     */
    public function getSchool();

    /**
     * @param Collection|UserInterface[] $directors
     */
    public function setDirectors(Collection $directors);

    /**
     * @param UserInterface $director
     */
    public function addDirector(UserInterface $director);

    /**
     * @return ArrayCollection|UserInterface[]
     */
    public function getDirectors();

    /**
     * @param Collection|CohortInterface[] $cohorts
     */
    public function setCohorts(Collection $cohorts);

    /**
     * @param CohortInterface $cohorts
     */
    public function addCohort(CohortInterface $cohorts);

    /**
     * @return ArrayCollection|CohortInterface[]
     */
    public function getCohorts();

    /**
     * @param DisciplineInterface $disciplines
     */
    public function addDiscipline(DisciplineInterface $disciplines);

    /**
     * @return ArrayCollection|DisciplineInterface[]
     */
    public function getDisciplines();

    /**
     * @param Collection|ObjectiveInterface[] $objectives
     */
    public function setObjectives(Collection $objectives);

    /**
     * @param ObjectiveInterface $objectives
     */
    public function addObjective(ObjectiveInterface $objectives);

    /**
     * @return ArrayCollection|ObjectiveInterface[]
     */
    public function getObjectives();

    /**
     * @param Collection|MeshDescriptorInterface[] $meshDescriptors
     */
    public function setMeshDescriptors(Collection $meshDescriptors);

    /**
     * @param MeshDescriptorInterface $meshDescriptors
     */
    public function addMeshDescriptor(MeshDescriptorInterface $meshDescriptors);

    /**
     * @return Collection|MeshDescriptorInterface[]
     */
    public function getMeshDescriptors();

    /**
     * @param PublishEventInterface $publishEvent
     */
    public function setPublishEvent(PublishEventInterface $publishEvent);

    /**
     * @return PublishEventInterface
     */
    public function getPublishEvent();
}

