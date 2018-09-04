<?php

namespace AppBundle\DataFixtures\ORM;

/**
 * Class LoadMeshDescriptorData
 */
class LoadMeshDescriptorData extends AbstractMeshFixture
{
    public function __construct()
    {
        parent::__construct('mesh_descriptor.csv', 'MeshDescriptor');
    }
}
