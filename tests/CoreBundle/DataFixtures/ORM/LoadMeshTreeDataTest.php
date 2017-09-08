<?php

namespace Tests\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\MeshTreeInterface;

/**
 * Class LoadMeshTreeDataTest
 */
class LoadMeshTreeDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'Ilios\CoreBundle\Entity\Manager\MeshTreeManager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
          'Ilios\CoreBundle\DataFixtures\ORM\LoadMeshTreeData',
        ];
    }

    /**
     * @covers \Ilios\CoreBundle\DataFixtures\ORM\LoadMeshTreeData::load
     * @group mesh_data_import
     */
    public function testLoad()
    {
        $this->runTestLoad('mesh_tree.csv', 10);
    }

    /**
     * @param array $data
     * @param MeshTreeInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `tree_number`,`mesh_descriptor_uid`,`mesh_tree_id`
        $this->assertEquals($data[0], $entity->getTreeNumber());
        $this->assertEquals($data[1], $entity->getDescriptor()->getId());
        $this->assertEquals($data[2], $entity->getId());
    }

    /**
     * @inheritdoc
     */
    protected function getEntity(array $data)
    {
        return $this->em->findOneBy(['id' => $data[2]]);
    }
}
