<?php

namespace Ilios\CoreBundle\Model;



/**
 * Interface MeshPreviousIndexingInterface
 */
interface MeshPreviousIndexingInterface 
{
    public function setMeshDescriptorUid($meshDescriptorUid);

    public function getMeshDescriptorUid();

    public function setPreviousIndexing($previousIndexing);

    public function getPreviousIndexing();
}
