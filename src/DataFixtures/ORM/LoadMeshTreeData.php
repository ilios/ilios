<?php

namespace App\DataFixtures\ORM;

use App\Entity\Manager\MeshDescriptorManager;
use App\Service\DataimportFileLocator;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class LoadMeshTreeData
 */
class LoadMeshTreeData extends AbstractMeshFixture implements DependentFixtureInterface
{
    public function __construct(
        MeshDescriptorManager $meshDescriptorManager,
        DataimportFileLocator $dataimportFileLocator
    ) {
        parent::__construct(
            $meshDescriptorManager,
            $dataimportFileLocator,
            'mesh_tree.csv',
            'MeshTree'
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
