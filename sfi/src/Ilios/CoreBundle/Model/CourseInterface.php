<?php

namespace Ilios\CoreBundle\Model;

/**
 * Interface CourseInterface
 */
interface CourseInterface 
{
    public function getCourseId();

    public function setTitle($title);

    public function getTitle();

    public function setCourseLevel($courseLevel);

    public function getCourseLevel();

    public function setYear($year);

    public function getYear();

    public function setStartDate($startDate);

    public function getStartDate();

    public function setEndDate($endDate);

    public function getEndDate();

    public function setDeleted($deleted);

    public function getDeleted();

    public function setExternalId($externalId);

    public function getExternalId();

    public function setLocked($locked);

    public function getLocked();

    public function setArchived($archived);

    public function getArchived();

    public function setPublishedAsTbd($publishedAsTbd);

    public function getPublishedAsTbd();

    public function setClerkshipType(\Ilios\CoreBundle\Model\CourseClerkshipType $clerkshipType = null);

    public function getClerkshipType();

    public function setOwningSchool(\Ilios\CoreBundle\Model\School $school = null);

    public function getOwningSchool();

    public function addDirector(\Ilios\CoreBundle\Model\User $directors);

    public function removeDirector(\Ilios\CoreBundle\Model\User $directors);

    public function getDirectors();

    public function addCohort(\Ilios\CoreBundle\Model\Cohort $cohorts);

    public function removeCohort(\Ilios\CoreBundle\Model\Cohort $cohorts);

    public function getCohorts();

    public function addDiscipline(\Ilios\CoreBundle\Model\Discipline $disciplines);

    public function removeDiscipline(\Ilios\CoreBundle\Model\Discipline $disciplines);

    public function getDisciplines();

    public function addObjective(\Ilios\CoreBundle\Model\Objective $objectives);

    public function removeObjective(\Ilios\CoreBundle\Model\Objective $objectives);

    public function getObjectives();

    public function addMeshDescriptor(\Ilios\CoreBundle\Model\MeshDescriptor $meshDescriptors);

    public function removeMeshDescriptor(\Ilios\CoreBundle\Model\MeshDescriptor $meshDescriptors);

    public function getMeshDescriptors();

    public function setPublishEvent(\Ilios\CoreBundle\Model\PublishEvent $publishEvent = null);

    public function getPublishEvent();
}

