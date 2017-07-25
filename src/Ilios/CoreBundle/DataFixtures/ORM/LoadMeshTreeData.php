<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;

/**
 * Class LoadMeshTreeData
 */
class LoadMeshTreeData extends AbstractMeshFixture implements DependentFixtureInterface
{
    public function __construct()
    {
        parent::__construct('mesh_tree.csv', 'MeshTree');
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
