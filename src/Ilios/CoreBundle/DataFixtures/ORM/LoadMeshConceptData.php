<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

/**
 * Class LoadMeshConceptData
 * @package Ilios\CoreBundle\DataFixtures\ORM
 */
class LoadMeshConceptData extends AbstractMeshFixture
{
    public function __construct()
    {
        parent::__construct('mesh_concept.csv', 'MeshConcept');
    }
}
