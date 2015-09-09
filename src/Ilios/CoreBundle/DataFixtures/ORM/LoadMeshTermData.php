<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

/**
 * Class LoadMeshTermData
 * @package Ilios\CoreBundle\DataFixtures\ORM
 */
class LoadMeshTermData extends AbstractMeshFixture
{
    public function __construct()
    {
        parent::__construct('mesh_term.csv', 'MeshTerm');
    }
}