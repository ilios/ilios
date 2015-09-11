<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

/**
 * Class LoadMeshDescriptorData
 * @package Ilios\CoreBundle\DataFixtures\ORM
 */
class LoadMeshDescriptorData extends AbstractMeshFixture
{
    public function __construct()
    {
        parent::__construct('mesh_descriptor.csv', 'MeshDescriptor');
    }
}
