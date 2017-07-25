<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class LoadMeshConceptTermData
 */
class LoadMeshConceptTermData extends AbstractMeshFixture implements DependentFixtureInterface
{
    public function __construct()
    {
        parent::__construct('mesh_concept_x_term.csv', 'MeshConceptTerm');
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
          'Ilios\CoreBundle\DataFixtures\ORM\LoadMeshConceptData',
          'Ilios\CoreBundle\DataFixtures\ORM\LoadMeshTermData',
        ];
    }
}
