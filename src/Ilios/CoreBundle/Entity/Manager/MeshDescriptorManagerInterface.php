<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\DTO\MeshDescriptorDTO;
use Ilios\CoreBundle\Entity\MeshDescriptorInterface;

/**
 * Interface MeshDescriptorManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface MeshDescriptorManagerInterface extends ManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return MeshDescriptorInterface
     */
    public function findMeshDescriptorBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return MeshDescriptorDTO
     */
    public function findMeshDescriptorDTOBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return MeshDescriptorInterface[]
     */
    public function findMeshDescriptorsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return MeshDescriptorDTO[]
     */
    public function findMeshDescriptorDTOsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param MeshDescriptorInterface $meshDescriptor
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateMeshDescriptor(
        MeshDescriptorInterface $meshDescriptor,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param MeshDescriptorInterface $meshDescriptor
     *
     * @return void
     */
    public function deleteMeshDescriptor(
        MeshDescriptorInterface $meshDescriptor
    );

    /**
     * @return MeshDescriptorInterface
     */
    public function createMeshDescriptor();

    /**
     * Single entry point for importing a given MeSH record into its corresponding database table.
     *
     * @param array $data An associative array containing a MeSH record.
     * @param string $type The type of MeSH data that's being imported.
     */
    public function import(array $data, $type);

    /**
     * @param string $q
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return MeshDescriptorInterface[]
     */
    public function findMeshDescriptorsByQ($q, array $orderBy = null, $limit = null, $offset = null);
}
