<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

/**
 * Class LoadMeshQualifierData
 */
class LoadMeshQualifierData extends AbstractMeshFixture
{
    public function __construct()
    {
        parent::__construct('mesh_qualifier.csv', 'MeshQualifier');
    }
}
