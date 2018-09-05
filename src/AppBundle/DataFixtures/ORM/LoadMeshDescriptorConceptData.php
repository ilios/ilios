<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Manager\MeshDescriptorManager;
use AppBundle\Service\DataimportFileLocator;
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
          'AppBundle\DataFixtures\ORM\LoadMeshDescriptorData',
          'AppBundle\DataFixtures\ORM\LoadMeshConceptData',
        ];
    }
}
