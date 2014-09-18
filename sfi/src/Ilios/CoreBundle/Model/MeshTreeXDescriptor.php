<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MeshTreeXDescriptor
 */
class MeshTreeXDescriptor
{
    /**
     * @var string
     */
    private $treeNumber;

    /**
     * @var string
     */
    private $meshDescriptorUid;


    /**
     * Set treeNumber
     *
     * @param string $treeNumber
     * @return MeshTreeXDescriptor
     */
    public function setTreeNumber($treeNumber)
    {
        $this->treeNumber = $treeNumber;

        return $this;
    }

    /**
     * Get treeNumber
     *
     * @return string 
     */
    public function getTreeNumber()
    {
        return $this->treeNumber;
    }

    /**
     * Set meshDescriptorUid
     *
     * @param string $meshDescriptorUid
     * @return MeshTreeXDescriptor
     */
    public function setMeshDescriptorUid($meshDescriptorUid)
    {
        $this->meshDescriptorUid = $meshDescriptorUid;

        return $this;
    }

    /**
     * Get meshDescriptorUid
     *
     * @return string 
     */
    public function getMeshDescriptorUid()
    {
        return $this->meshDescriptorUid;
    }
}
