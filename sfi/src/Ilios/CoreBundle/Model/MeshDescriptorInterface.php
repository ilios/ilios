<?php

namespace Ilios\CoreBundle\Model;



/**
 * Interface MeshDescriptorInterface
 */
interface MeshDescriptorInterface 
{
    public function setMeshDescriptorUid($meshDescriptorUid);

    public function getMeshDescriptorUid();

    public function setName($name);

    public function getName();

    public function setAnnotation($annotation);

    public function getAnnotation();

    public function setCreatedAt($createdAt);

    public function getCreatedAt();

    public function setUpdatedAt($updatedAt);

    public function getUpdatedAt();

    public function addCourse(\Ilios\CoreBundle\Model\Course $courses);

    public function removeCourse(\Ilios\CoreBundle\Model\Course $courses);

    public function getCourses();

    public function addObjective(\Ilios\CoreBundle\Model\Objective $objectives);

    public function removeObjective(\Ilios\CoreBundle\Model\Objective $objectives);

    public function getObjectives();

    public function addSession(\Ilios\CoreBundle\Model\Session $sessions);

    public function removeSession(\Ilios\CoreBundle\Model\Session $sessions);

    public function getSessions();

    public function addSessionLearningMaterial(;

    public function removeSessionLearningMaterial(;

    public function getSessionLearningMaterials();

    public function addCourseLearningMaterial(\Ilios\CoreBundle\Model\CourseLearningMaterial $courseLearningMaterials);

    public function removeCourseLearningMaterial(;

    public function getCourseLearningMaterials();
}
