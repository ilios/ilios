<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class LoadMeshDescriptorConceptData
 */
class LoadMeshDescriptorConceptData extends AbstractMeshFixture implements DependentFixtureInterface
{
    public function __construct()
    {
        parent::__construct('mesh_descriptor_x_concept.csv', 'MeshDescriptorConcept');
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
          'Ilios\CoreBundle\DataFixtures\ORM\LoadMeshDescriptorData',
          'Ilios\CoreBundle\DataFixtures\ORM\LoadMeshConceptData',
        ];
    }
}
