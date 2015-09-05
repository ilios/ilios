<?php

namespace Ilios\CoreBundle\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\MeshTerm;
use Ilios\CoreBundle\Entity\MeshTermInterface;

/**
 * Class LoadMeshTermData
 * @package Ilios\CoreBundle\DataFixtures\ORM
 */
class LoadMeshTermData extends AbstractFixture
{
    public function __construct()
    {
        parent::__construct('mesh_term');
    }
    
    /**
     * @return MeshTermInterface
     *
     * @see AbstractFixture::createEntity()
     */
    protected function createEntity()
    {
        return new MeshTerm();
    }

    /**
     * @param MeshTermInterface $entity
     * @param array $data
     * @return MeshTermInterface
     *
     * @see AbstractFixture::populateEntity()
     */
    protected function populateEntity($entity, array $data)
    {
        // `mesh_term_uid`,`name`,`lexical_tag`,`concept_preferred`,
        // `record_preferred`,`permuted`,`print`,`created_at`,`updated_at`,
        // `mesh_term_id`

        $entity->setMeshTermUid($data[0]);
        $entity->setName($data[1]);
        $entity->setLexicalTag($data[2]);
        $entity->setConceptPreferred((boolean) $data[3]);
        $entity->setRecordPreferred((boolean) $data[4]);
        $entity->setPermuted((boolean) $data[5]);
        $entity->setPrintable((boolean) $data[6]);
        $entity->setCreatedAt(new \DateTime($data[7], new \DateTimeZone('UTC')));
        $entity->setUpdatedAt(new \DateTime($data[8], new \DateTimeZone('UTC')));
        $entity->setId($data[9]);

        return $entity;
    }
}
