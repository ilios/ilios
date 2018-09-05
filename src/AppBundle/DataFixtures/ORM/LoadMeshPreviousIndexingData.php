<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Manager\MeshDescriptorManager;
use AppBundle\Service\DataimportFileLocator;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class LoadMeshPreviousIndexingData
 */
class LoadMeshPreviousIndexingData extends AbstractMeshFixture implements DependentFixtureInterface
{
    public function __construct(
        MeshDescriptorManager $meshDescriptorManager,
        DataimportFileLocator $dataimportFileLocator
    ) {
        parent::__construct(
            $meshDescriptorManager,
            $dataimportFileLocator,
            'mesh_previous_indexing.csv',
            'MeshPreviousIndexing'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
          'AppBundle\DataFixtures\ORM\LoadMeshDescriptorData',
        ];
    }
}
