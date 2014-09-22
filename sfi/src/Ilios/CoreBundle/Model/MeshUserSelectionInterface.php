<?php

namespace Ilios\CoreBundle\Model;

/**
 * Interface MeshUserSelectionInterface
 */
interface MeshUserSelectionInterface 
{
    public function getMeshUserSelectionId();

    public function setMeshDescriptorUid($meshDescriptorUid);

    public function getMeshDescriptorUid();

    public function setSearchPhrase($searchPhrase);

    public function getSearchPhrase();
}

