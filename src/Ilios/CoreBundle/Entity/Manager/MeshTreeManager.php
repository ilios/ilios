<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\MeshTreeInterface;

/**
 * MeshTree manager service.
 * Class MeshTreeManager
 * @package Ilios\CoreBundle\Manager
 */
class MeshTreeManager extends AbstractManager implements MeshTreeManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return MeshTreeInterface
     */
    public function findMeshTreeBy(array $criteria, array $orderBy = null)
    {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return MeshTreeInterface[]|Collection
     */
    public function findMeshTreesBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param MeshTreeInterface $meshTree
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateMeshTree(MeshTreeInterface $meshTree, $andFlush = true, $forceId = false)
    {
        $this->em->persist($meshTree);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($meshTree));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param MeshTreeInterface $meshTree
     */
    public function deleteMeshTree(MeshTreeInterface $meshTree)
    {
        $this->em->remove($meshTree);
        $this->em->flush();
    }

    /**
     * @return MeshTreeInterface
     */
    public function createMeshTree()
    {
        $class = $this->getClass();
        return new $class();
    }
}
