<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MeshUserSelection
 */
class MeshUserSelection
{
    /**
     * @var integer
     */
    private $meshUserSelectionId;

    /**
     * @var string
     */
    private $meshDescriptorUid;

    /**
     * @var string
     */
    private $searchPhrase;


    /**
     * Get meshUserSelectionId
     *
     * @return integer 
     */
    public function getMeshUserSelectionId()
    {
        return $this->meshUserSelectionId;
    }

    /**
     * Set meshDescriptorUid
     *
     * @param string $meshDescriptorUid
     * @return MeshUserSelection
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
     * Set searchPhrase
     *
     * @param string $searchPhrase
     * @return MeshUserSelection
     */
    public function setSearchPhrase($searchPhrase)
    {
        $this->searchPhrase = $searchPhrase;

        return $this;
    }

    /**
     * Get searchPhrase
     *
     * @return string 
     */
    public function getSearchPhrase()
    {
        return $this->searchPhrase;
    }
}
