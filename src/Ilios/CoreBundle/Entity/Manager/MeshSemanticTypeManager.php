<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\MeshSemanticTypeInterface;

/**
 * Class MeshSemanticTypeManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class MeshSemanticTypeManager extends AbstractManager implements MeshSemanticTypeManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return MeshSemanticTypeInterface
     */
    public function findMeshSemanticTypeBy(
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
     * @return ArrayCollection|MeshSemanticTypeInterface[]
     */
    public function findMeshSemanticTypesBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param MeshSemanticTypeInterface $meshSemanticType
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateMeshSemanticType(
        MeshSemanticTypeInterface $meshSemanticType,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($meshSemanticType);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($meshSemanticType));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param MeshSemanticTypeInterface $meshSemanticType
     */
    public function deleteMeshSemanticType(
        MeshSemanticTypeInterface $meshSemanticType
    ) {
        $this->em->remove($meshSemanticType);
        $this->em->flush();
    }

    /**
     * @return MeshSemanticTypeInterface
     */
    public function createMeshSemanticType()
    {
        $class = $this->getClass();
        return new $class();
    }
}
