<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

/**
 * Class LoadMeshSemanticTypeData
 * @deprecated
 */
class LoadMeshSemanticTypeData extends AbstractMeshFixture
{
    public function __construct()
    {
        parent::__construct('mesh_semantic_type.csv', 'MeshSemanticType');
    }
}
