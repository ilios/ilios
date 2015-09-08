<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Ilios\CoreBundle\Entity\MeshTree;
use Ilios\CoreBundle\Entity\MeshTreeInterface;

/**
 * Class LoadMeshTreeData
 * @package Ilios\CoreBundle\DataFixtures\ORM
 */
class LoadMeshTreeData extends AbstractFixture implements DependentFixtureInterface
{
    public function __construct()
    {
        parent::__construct('mesh_tree_x_descriptor');
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

    /**
     * @return MeshTreeInterface
     *
     * @see AbstractFixture::createEntity()
     */
    protected function createEntity()
    {
        return new MeshTree();
    }

    /**
     * @param MeshTreeInterface $entity
     * @param array $data
     * @return MeshTreeInterface
     *
     * @see AbstractFixture::populateEntity()
     */
    protected function populateEntity($entity, array $data)
    {
        // `tree_number`,`mesh_descriptor_uid`,`mesh_tree_id`
        $entity->setTreeNumber($data[0]);
        $entity->setDescriptor($this->getReference('mesh_descriptor' .$data[1]));
        $entity->setId($data[2]);

        return $entity;
    }
}
