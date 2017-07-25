<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

/**
 * Class LoadMeshConceptData
 */
class LoadMeshConceptData extends AbstractMeshFixture
{
    public function __construct()
    {
        parent::__construct('mesh_concept.csv', 'MeshConcept');
    }
}
