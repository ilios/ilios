<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\MeshTermInterface;

/**
 * MeshTerm manager service.
 * Class MeshTermManager
 * @package Ilios\CoreBundle\Manager
 */
class MeshTermManager extends AbstractManager implements MeshTermManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return MeshTermInterface
     */
    public function findMeshTermBy(array $criteria, array $orderBy = null)
    {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return MeshTermInterface[]|Collection
     */
    public function findMeshTermsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param MeshTermInterface $meshTerm
     * @param bool $andFlush
     * @param bool $forceId
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
     * @param MeshTermInterface $meshTerm
     */
    public function deleteMeshTerm(MeshTermInterface $meshTerm)
    {
        $this->em->remove($meshTerm);
        $this->em->flush();
    }

    /**
     * @return MeshTermInterface
     */
    public function createMeshTerm()
    {
        $class = $this->getClass();
        return new $class();
    }
}
