<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\MeshDescriptor;
use Ilios\CoreBundle\Entity\MeshDescriptorInterface;

/**
 * Class LoadMeshDescriptorData
 * @package Ilios\CoreBundle\DataFixtures\ORM
 */
class LoadMeshDescriptorData extends AbstractFixture
{
    public function __construct()
    {
        parent::__construct('mesh_descriptor');
    }

    /**
     * @return MeshDescriptorInterface
     *
     * @see AbstractFixture::createEntity()
     */
    protected function createEntity()
    {
        return new MeshDescriptor();
    }

    /**
     * @param MeshDescriptorInterface $entity
     * @param array $data
     * @return MeshDescriptorInterface
     *
     * @see AbstractFixture::populateEntity()
     */
    protected function populateEntity($entity, array $data)
    {
        // `mesh_descriptor_uid`,`name`,`annotation`,`created_at`,`updated_at`
        $entity->setId($data[0]);
        $entity->setName($data[1]);
        $entity->setAnnotation($data[2]);
        $entity->setCreatedAt(new \DateTime($data[3], new \DateTimeZone('UTC')));
        $entity->setUpdatedAt(new \DateTime($data[4], new \DateTimeZone('UTC')));

        return $entity;
    }
}
