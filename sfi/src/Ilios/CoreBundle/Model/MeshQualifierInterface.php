<?php

namespace Ilios\CoreBundle\Model;

/**
 * Interface MeshQualifierInterface
 */
interface MeshQualifierInterface 
{
    public function setMeshQualifierUid($meshQualifierUid);

    public function getMeshQualifierUid();

    public function setName($name);

    public function getName();

    public function setCreatedAt($createdAt);

    public function getCreatedAt();

    public function setUpdatedAt($updatedAt);

    public function getUpdatedAt();
}

