<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\MeshQualifierInterface;

/**
 * Class MeshQualifierManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class MeshQualifierManager extends BaseManager implements MeshQualifierManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findMeshQualifierBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findMeshQualifiersBy(
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
     * {@inheritdoc}
     */
    public function deleteMeshQualifier(
        MeshQualifierInterface $meshQualifier
    ) {
        $this->em->remove($meshQualifier);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createMeshQualifier()
    {
        $class = $this->getClass();
        return new $class();
    }
}
