<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\MeshQualifier;
use Ilios\CoreBundle\Entity\MeshQualifierInterface;

/**
 * Class LoadMeshQualifierData
 * @package Ilios\CoreBundle\DataFixtures\ORM
 */
class LoadMeshQualifierData extends AbstractFixture
{
    public function __construct()
    {
        parent::__construct('mesh_qualifier');
    }

    /**
     * @return MeshQualifierInterface
     *
     * @see AbstractFixture::createEntity()
     */
    protected function createEntity()
    {
        return new MeshQualifier();
    }

    /**
     * @param MeshQualifierInterface $entity
     * @param array $data
     * @return MeshQualifierInterface
     *
     * @see AbstractFixture::populateEntity()
     */
    protected function populateEntity($entity, array $data)
    {
        // `mesh_qualifier_uid`,`name`,`created_at`,`updated_at`
        $entity->setId($data[0]);
        $entity->setName($data[1]);
        $entity->setCreatedAt(new \DateTime($data[2], new \DateTimeZone('UTC')));
        $entity->setUpdatedAt(new \DateTime($data[3], new \DateTimeZone('UTC')));

        return $entity;
    }
}
