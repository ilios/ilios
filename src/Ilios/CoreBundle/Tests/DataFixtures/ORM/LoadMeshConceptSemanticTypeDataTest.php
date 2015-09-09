<?php

namespace Ilios\CoreBundle\Tests\DataFixtures\ORM;

use Ilios\CoreBundle\Entity\Manager\MeshConceptManagerInterface;
use Ilios\CoreBundle\Entity\MeshConceptInterface;
use Ilios\CoreBundle\Entity\MeshSemanticTypeInterface;

/**
 * Class LoadMeshConceptSemanticTypeDataTest
 * @package Ilios\CoreBundle\Tests\DataFixtures\ORM
 */
class LoadMeshConceptSemanticTypeDataTest extends AbstractDataFixtureTest
{
    /**
     * {@inheritdoc}
     */
    public function getEntityManagerServiceKey()
    {
        return 'ilioscore.meshconcept.manager';
    }

    /**
     * {@inheritdoc}
     */
    public function getFixtures()
    {
        return [
          'Ilios\CoreBundle\DataFixtures\ORM\LoadMeshConceptSemanticTypeData',
        ];
    }

    /**
     * @covers Ilios\CoreBundle\DataFixtures\ORM\LoadMeshConceptSemanticTypeData::load
     */
    public function testLoad()
    {
        $this->runTestLoad('mesh_concept_x_semantic_type.csv', 10);
    }

    /**
     * @param array $data
     * @param MeshConceptInterface $entity
     */
    protected function assertDataEquals(array $data, $entity)
    {
        // `mesh_concept_uid`,`mesh_semantic_type_uid`
        $this->assertEquals($data[0], $entity->getId());
        // find the semantic type
        $semanticTypeId = $data[0];
        $semanticType = $entity->getSemanticTypes()->filter(
            function (MeshSemanticTypeInterface $semanticType) use ($semanticTypeId) {
                return $semanticType->getId() === $semanticTypeId;
            }
        )->first();
        $this->assertNotEmpty($semanticType);
    }

    /**
     * @param array $data
     * @return MeshConceptInterface
     * @override
     */
    protected function getEntity(array $data)
    {
        /**
         * @var MeshConceptManagerInterface $em
         */
        $em = $this->em;
        return $em->findMeshConceptBy(['id' => $data[0]]);
    }
}
