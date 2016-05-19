<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\MeshTermInterface;

/**
 * MeshTerm manager service.
 * Class MeshTermManager
 * @package Ilios\CoreBundle\Manager
 */
class MeshTermManager extends BaseManager implements MeshTermManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findMeshTermBy(array $criteria, array $orderBy = null)
    {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findMeshTermsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function updateMeshTerm(MeshTermInterface $meshTerm, $andFlush = true, $forceId = false)
    {
        $this->em->persist($meshTerm);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($meshTerm));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMeshTerm(MeshTermInterface $meshTerm)
    {
        $this->em->remove($meshTerm);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createMeshTerm()
    {
        $class = $this->getClass();
        return new $class();
    }
}
