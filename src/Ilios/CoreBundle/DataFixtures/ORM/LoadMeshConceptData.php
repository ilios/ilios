<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\MeshConcept;
use Ilios\CoreBundle\Entity\MeshConceptInterface;

/**
 * Class LoadMeshConceptData
 * @package Ilios\CoreBundle\DataFixtures\ORM
 */
class LoadMeshConceptData extends AbstractFixture
{
    public function __construct()
    {
        parent::__construct('mesh_concept');
    }

    /**
     * @return MeshConceptInterface
     *
     * @see AbstractFixture::createEntity()
     */
    protected function createEntity()
    {
        return new MeshConcept();
    }

    /**
     * @param MeshConceptInterface $entity
     * @param array $data
     * @return MeshConceptInterface
     *
     * @see AbstractFixture::populateEntity()
     */
    protected function populateEntity($entity, array $data)
    {
        // `mesh_concept_uid`,`name`,`umls_uid`,`preferred`,`scope_note`,
        // `casn_1_name`,`registry_number`,`created_at`,`updated_at`
        $entity->setId($data[0]);
        $entity->setName($data[1]);
        $entity->setUmlsUid($data[2]);
        $entity->setPreferred((boolean) $data[3]);
        $entity->setScopeNote($data[4]);
        $entity->setCasn1Name($data[5]);
        $entity->setRegistryNumber($data[6]);
        $entity->setCreatedAt(new \DateTime($data[7], new \DateTimeZone('UTC')));
        $entity->setUpdatedAt(new \DateTime($data[8], new \DateTimeZone('UTC')));

        return $entity;
    }
}
