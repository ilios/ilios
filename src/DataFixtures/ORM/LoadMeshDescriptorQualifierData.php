<?php

namespace App\DataFixtures\ORM;

use App\Entity\Manager\MeshDescriptorManager;
use App\Service\DataimportFileLocator;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class LoadMeshDescriptorQualifierData
 */
class LoadMeshDescriptorQualifierData extends AbstractMeshFixture implements DependentFixtureInterface
{
    public function __construct(
        MeshDescriptorManager $meshDescriptorManager,
        DataimportFileLocator $dataimportFileLocator
    ) {
        parent::__construct(
            $meshDescriptorManager,
            $dataimportFileLocator,
            'mesh_descriptor_x_qualifier.csv',
            'MeshDescriptorQualifier'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
          'App\DataFixtures\ORM\LoadMeshDescriptorData',
          'App\DataFixtures\ORM\LoadMeshQualifierData',
        ];
    }
}
