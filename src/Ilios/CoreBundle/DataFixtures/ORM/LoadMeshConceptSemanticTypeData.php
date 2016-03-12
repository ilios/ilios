<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class LoadMeshConceptSemanticTypeData
 * @package Ilios\CoreBundle\DataFixtures\ORM
 * @deprecated
 */
class LoadMeshConceptSemanticTypeData extends AbstractMeshFixture implements DependentFixtureInterface
{
    public function __construct()
    {
        parent::__construct('mesh_concept_x_semantic_type.csv', 'MeshConceptSemanticType');
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
          'Ilios\CoreBundle\DataFixtures\ORM\LoadMeshConceptData',
          'Ilios\CoreBundle\DataFixtures\ORM\LoadMeshSemanticTypeData',
        ];
    }
}
