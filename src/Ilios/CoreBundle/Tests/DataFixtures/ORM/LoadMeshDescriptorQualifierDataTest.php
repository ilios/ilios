<?php

namespace Ilios\CoreBundle\Tests\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\Manager\MeshDescriptorManagerInterface;
use Ilios\CoreBundle\Entity\MeshDescriptorInterface;
use Ilios\CoreBundle\Entity\MeshQualifierInterface;

/**
 * Class LoadMeshDescriptorQualifierDataTest
 * @package Ilios\CoreBundle\Tests\DataFixtures\ORM
 */
class LoadMeshDescriptorQualifierDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'ilioscore.meshdescriptor.manager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
          'Ilios\CoreBundle\DataFixtures\ORM\LoadMeshDescriptorQualifierData',
        ];
    }

    /**
     * @covers Ilios\CoreBundle\DataFixtures\ORM\LoadMeshDescriptorQualifierData::load
     */
    public function testLoad()
    {
        $this->runTestLoad('mesh_descriptor_x_qualifier.csv', 10);
    }

    /**
     * @param array $data
     * @param MeshDescriptorInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `mesh_descriptor_uid`,`mesh_qualifier_uid`
        $this->assertEquals($data[0], $entity->getId());
        // find the qualifier
        $qualifierId = $data[1];
        $qualifier = $entity->getQualifiers()->filter(function (MeshQualifierInterface $qualifier) use ($qualifierId) {
            return $qualifier->getId() === $qualifierId;
        })->first();
        $this->assertNotEmpty($qualifier);
    }

    /**
     * @param array $data
     * @return MeshDescriptorInterface
     * @override
     */
    protected function getEntity(array $data)
    {
        /**
         * @var MeshDescriptorManagerInterface $em
         */
        $em = $this->em;
        return $em->findMeshDescriptorBy(['id' => $data[0]]);
    }
}
