<?php

namespace App\DataFixtures\ORM;

use App\Entity\Manager\MeshDescriptorManager;
use App\Service\DataimportFileLocator;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class LoadMeshDescriptorConceptData
 */
class LoadMeshDescriptorConceptData extends AbstractMeshFixture implements DependentFixtureInterface
{
    public function __construct(
        MeshDescriptorManager $meshDescriptorManager,
        DataimportFileLocator $dataimportFileLocator
    ) {
        parent::__construct(
            $meshDescriptorManager,
            $dataimportFileLocator,
            'mesh_descriptor_x_concept.csv',
            'MeshDescriptorConcept'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
          'App\DataFixtures\ORM\LoadMeshDescriptorData',
          'App\DataFixtures\ORM\LoadMeshConceptData',
        ];
    }
}
