<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\MeshSemanticType;
use Ilios\CoreBundle\Entity\MeshSemanticTypeInterface;

/**
 * Class LoadMeshSemanticTypeData
 * @package Ilios\CoreBundle\DataFixtures\ORM
 */
class LoadMeshSemanticTypeData extends AbstractFixture
{
    public function __construct()
    {
        parent::__construct('mesh_semantic_type');
    }

    /**
     * @return MeshSemanticTypeInterface
     *
     * @see AbstractFixture::createEntity()
     */
    protected function createEntity()
    {
        return new MeshSemanticType();
    }

    /**
     * @param MeshSemanticTypeInterface $entity
     * @param array $data
     * @return MeshSemanticTypeInterface
     *
     * @see AbstractFixture::populateEntity()
     */
    protected function populateEntity($entity, array $data)
    {
        // `mesh_semantic_type_uid`,`name`,`created_at`,`updated_at`
        $entity->setId($data[0]);
        $entity->setName($data[1]);
        $entity->setCreatedAt(new \DateTime($data[2], new \DateTimeZone('UTC')));
        $entity->setUpdatedAt(new \DateTime($data[3], new \DateTimeZone('UTC')));

        return $entity;
    }
}
