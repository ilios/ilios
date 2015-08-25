<?php

namespace Ilios\CoreBundle\Entity;

/**
 * Class MeshTreeInterface
 * @package Ilios\CoreBundle\Entity
 */
interface MeshTreeInterface
{

    /**
     * Set treeNumber
     *
     * @param string $treeNumber
     *
     * @return MeshTree
     */
    public function setTreeNumber($treeNumber);

    /**
     * Get treeNumber
     *
     * @return string
     */
    public function getTreeNumber();

    /**
     * Set meshDescriptor
     *
     * @param MeshDescriptorInterface $descriptor
     *
     * @return MeshTree
     */
    public function setDescriptor(MeshDescriptorInterface $descriptor);

    /**
     * Get meshDescriptor
     *
     * @return MeshDescriptorInterface
     */
    public function getDescriptor();

}
