<?php

namespace Ilios\CoreBundle\Model;



/**
 * Interface MeshTreeXDescriptorInterface
 */
interface MeshTreeXDescriptorInterface 
{
    public function setTreeNumber($treeNumber);

    public function getTreeNumber();

    public function setMeshDescriptorUid($meshDescriptorUid);

    public function getMeshDescriptorUid();
}
