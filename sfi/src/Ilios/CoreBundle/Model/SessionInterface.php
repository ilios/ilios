<?php

namespace Ilios\CoreBundle\Model;

/**
 * Interface SessionInterface
 */
interface SessionInterface 
{
    public function getSessionId();

    public function setTitle($title);

    public function getTitle();

    public function setAttireRequired($attireRequired);

    public function getAttireRequired();

    public function setEquipmentRequired($equipmentRequired);

    public function getEquipmentRequired();

    public function setSupplemental($supplemental);

    public function getSupplemental();

    public function setDeleted($deleted);

    public function getDeleted();

    public function setPublishedAsTbd($publishedAsTbd);

    public function getPublishedAsTbd();

    public function setLastUpdatedOn($lastUpdatedOn);

    public function getLastUpdatedOn();

    public function setSessionType(\Ilios\CoreBundle\Model\SessionType $sessionType = null);

    public function getSessionType();

    public function setCourse(\Ilios\CoreBundle\Model\Course $course = null);

    public function getCourse();

    public function setIlmSessionFacet(\Ilios\CoreBundle\Model\IlmSessionFacet $ilmSessionFacet = null);

    public function getIlmSessionFacet();

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

