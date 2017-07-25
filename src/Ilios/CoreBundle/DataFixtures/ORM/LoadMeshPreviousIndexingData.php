<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class LoadMeshPreviousIndexingData
 */
class LoadMeshPreviousIndexingData extends AbstractMeshFixture implements DependentFixtureInterface
{
    public function __construct()
    {
        parent::__construct('mesh_previous_indexing.csv', 'MeshPreviousIndexing');
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
          'Ilios\CoreBundle\DataFixtures\ORM\LoadMeshDescriptorData',
        ];
    }
}
