<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\MeshQualifierInterface;

/**
 * Interface MeshQualifierManagerInterface
 * @package Ilios\CoreBundle\Manager
 */
interface MeshQualifierManagerInterface
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
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return MeshQualifierInterface[]|Collection
     */
    public function findMeshQualifiersBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param MeshQualifierInterface $meshQualifier
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateMeshQualifier(
        MeshQualifierInterface $meshQualifier,
        $andFlush = true
    );

    /**
     * @param MeshQualifierInterface $meshQualifier
     *
     * @return void
     */
    public function deleteMeshQualifier(
        MeshQualifierInterface $meshQualifier
    );

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return MeshQualifierInterface
     */
    public function createMeshQualifier();
}
