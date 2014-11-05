<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * MeshUserSelection
 */
class MeshUserSelection
{
    /**
     * @var int
     */
    protected $meshUserSelectionId;

    /**
     * @var string
     */
    protected $meshDescriptorUid;

    /**
     * @var string
     */
    protected $searchPhrase;


    /**
     * Get meshUserSelectionId
     *
     * @return int 
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
