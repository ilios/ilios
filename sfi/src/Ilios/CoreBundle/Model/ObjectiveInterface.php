<?php

namespace Ilios\CoreBundle\Model;



/**
 * Interface ObjectiveInterface
 */
interface ObjectiveInterface 
{
    public function getObjectiveId();

    public function setTitle($title);

    public function getTitle();

    public function setCompetency(\Ilios\CoreBundle\Model\Competency $competency = null);

    public function getCompetency();

    public function getCompetencyId();

    public function addCourse(\Ilios\CoreBundle\Model\Course $courses);

    public function removeCourse(\Ilios\CoreBundle\Model\Course $courses);

    public function getCourses();

    public function addProgramYear(\Ilios\CoreBundle\Model\ProgramYear $programYears);

    public function removeProgramYear(\Ilios\CoreBundle\Model\ProgramYear $programYears);

    public function getProgramYears();

    public function addSession(\Ilios\CoreBundle\Model\Session $sessions);

    public function removeSession(\Ilios\CoreBundle\Model\Session $sessions);

    public function getSessions();

    public function addChild(\Ilios\CoreBundle\Model\Objective $children);

    public function removeChild(\Ilios\CoreBundle\Model\Objective $children);

    public function getChildren();

    public function addMeshDescriptor(\Ilios\CoreBundle\Model\MeshDescriptor $meshDescriptors);

    public function removeMeshDescriptor(\Ilios\CoreBundle\Model\MeshDescriptor $meshDescriptors);

    public function getMeshDescriptors();

    public function addParent(\Ilios\CoreBundle\Model\Objective $parents);

    public function removeParent(\Ilios\CoreBundle\Model\Objective $parents);

    public function getParents();
}
