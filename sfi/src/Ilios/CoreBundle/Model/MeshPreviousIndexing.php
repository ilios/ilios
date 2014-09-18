<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MeshPreviousIndexing
 */
class MeshPreviousIndexing
{
    /**
     * @var string
     */
    private $meshDescriptorUid;

    /**
     * @var string
     */
    private $previousIndexing;


    /**
     * Set meshDescriptorUid
     *
     * @param string $meshDescriptorUid
     * @return MeshPreviousIndexing
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

    /**
     * Set previousIndexing
     *
     * @param string $previousIndexing
     * @return MeshPreviousIndexing
     */
    public function setPreviousIndexing($previousIndexing)
    {
        $this->previousIndexing = $previousIndexing;

        return $this;
    }

    /**
     * Get previousIndexing
     *
     * @return string 
     */
    public function getPreviousIndexing()
    {
        return $this->previousIndexing;
    }
}
