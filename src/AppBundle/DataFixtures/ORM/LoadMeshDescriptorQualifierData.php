<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class LoadMeshDescriptorQualifierData
 */
class LoadMeshDescriptorQualifierData extends AbstractMeshFixture implements DependentFixtureInterface
{
    public function __construct()
    {
        parent::__construct('mesh_descriptor_x_qualifier.csv', 'MeshDescriptorQualifier');
    }

    /**
     * {@inheritdoc}
     */
    public function getDependencies()
    {
        return [
          'AppBundle\DataFixtures\ORM\LoadMeshDescriptorData',
          'AppBundle\DataFixtures\ORM\LoadMeshQualifierData',
        ];
    }
}
