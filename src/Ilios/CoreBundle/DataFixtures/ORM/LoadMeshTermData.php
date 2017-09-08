<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

/**
 * Class LoadMeshTermData
 */
class LoadMeshTermData extends AbstractMeshFixture
{
    public function __construct()
    {
        parent::__construct('mesh_term.csv', 'MeshTerm');
    }
}
