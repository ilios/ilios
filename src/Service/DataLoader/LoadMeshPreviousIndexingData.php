<?php

declare(strict_types=1);

namespace App\Service\DataLoader;

use App\Repository\MeshDescriptorRepository;
use App\Service\DataimportFileLocator;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class LoadMeshPreviousIndexingData
 */
class LoadMeshPreviousIndexingData extends AbstractMeshFixture implements DependentFixtureInterface
{
    public function __construct(
        MeshDescriptorRepository $meshDescriptorRepository,
        DataimportFileLocator $dataimportFileLocator
    ) {
        parent::__construct(
            $meshDescriptorRepository,
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
          'App\DataFixtures\ORM\LoadMeshDescriptorData',
        ];
    }
}
