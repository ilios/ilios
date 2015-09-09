<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

/**
 * Class LoadMeshQualifierData
 * @package Ilios\CoreBundle\DataFixtures\ORM
 */
class LoadMeshQualifierData extends AbstractMeshFixture
{
    public function __construct()
    {
        parent::__construct('mesh_qualifier.csv', 'MeshQualifier');
    }
}
