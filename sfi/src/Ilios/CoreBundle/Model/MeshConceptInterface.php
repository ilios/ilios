<?php

namespace Ilios\CoreBundle\Model;

/**
 * Interface MeshConceptInterface
 */
interface MeshConceptInterface 
{
    public function setMeshConceptUid($meshConceptUid);

    public function getMeshConceptUid();

    public function setName($name);

    public function getName();

    public function setUmlsUid($umlsUid);

    public function getUmlsUid();

    public function setPreferred($preferred);

    public function getPreferred();

    public function setScopeNote($scopeNote);

    public function getScopeNote();

    public function setCasn1Name($casn1Name);

    public function getCasn1Name();

    public function setRegistryNumber($registryNumber);

    public function getRegistryNumber();

    public function setCreatedAt($createdAt);

    public function getCreatedAt();

    public function setUpdatedAt($updatedAt);

    public function getUpdatedAt();
}

