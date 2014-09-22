<?php

namespace Ilios\CoreBundle\Model;



/**
 * Interface MeshSemanticTypeInterface
 */
interface MeshSemanticTypeInterface 
{
    public function setMeshSemanticTypeUid($meshSemanticTypeUid);

    public function getMeshSemanticTypeUid();

    public function setName($name);

    public function getName();

    public function setCreatedAt($createdAt);

    public function getCreatedAt();

    public function setUpdatedAt($updatedAt);

    public function getUpdatedAt();
}
