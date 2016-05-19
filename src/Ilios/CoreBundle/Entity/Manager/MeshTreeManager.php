<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\MeshTreeInterface;

/**
 * MeshTree manager service.
 * Class MeshTreeManager
 * @package Ilios\CoreBundle\Manager
 */
class MeshTreeManager extends BaseManager implements MeshTreeManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findMeshTreeBy(array $criteria, array $orderBy = null)
    {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findMeshTreesBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
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
     * {@inheritdoc}
     */
    public function deleteMeshTree(MeshTreeInterface $meshTree)
    {
        $this->em->remove($meshTree);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createMeshTree()
    {
        $class = $this->getClass();
        return new $class();
    }
}
