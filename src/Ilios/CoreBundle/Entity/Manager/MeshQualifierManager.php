<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\MeshQualifierInterface;

/**
 * Class MeshQualifierManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class MeshQualifierManager extends AbstractManager implements MeshQualifierManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return MeshQualifierInterface
     */
    public function findMeshQualifierBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|MeshQualifierInterface[]
     */
    public function findMeshQualifiersBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param MeshQualifierInterface $meshQualifier
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateMeshQualifier(
        MeshQualifierInterface $meshQualifier,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($meshQualifier);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($meshQualifier));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param MeshQualifierInterface $meshQualifier
     */
    public function deleteMeshQualifier(
        MeshQualifierInterface $meshQualifier
    ) {
        $this->em->remove($meshQualifier);
        $this->em->flush();
    }

    /**
     * @return MeshQualifierInterface
     */
    public function createMeshQualifier()
    {
        $class = $this->getClass();
        return new $class();
    }
}
