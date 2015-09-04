<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\MeshConceptInterface;

/**
 * Class MeshConceptManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class MeshConceptManager extends AbstractManager implements MeshConceptManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return MeshConceptInterface
     */
    public function findMeshConceptBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|MeshConceptInterface[]
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
     * @param MeshConceptInterface $meshConcept
     * @param bool $andFlush
     * @param bool $forceId
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
     * @param MeshConceptInterface $meshConcept
     */
    public function deleteMeshConcept(
        MeshConceptInterface $meshConcept
    ) {
        $this->em->remove($meshConcept);
        $this->em->flush();
    }

    /**
     * @return MeshConceptInterface
     */
    public function createMeshConcept()
    {
        $class = $this->getClass();
        return new $class();
    }
}
