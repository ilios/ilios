<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\MeshConceptInterface;

/**
 * Class MeshConceptManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class MeshConceptManager extends BaseManager implements MeshConceptManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findMeshConceptBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findMeshConceptsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function updateMeshConcept(
        MeshConceptInterface $meshConcept,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($meshConcept);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($meshConcept));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMeshConcept(
        MeshConceptInterface $meshConcept
    ) {
        $this->em->remove($meshConcept);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createMeshConcept()
    {
        $class = $this->getClass();
        return new $class();
    }
}
