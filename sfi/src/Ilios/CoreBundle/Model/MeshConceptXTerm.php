<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MeshConceptXTerm
 */
class MeshConceptXTerm
{
    /**
     * @var string
     */
    private $meshConceptUid;

    /**
     * @var string
     */
    private $meshTermUid;


    /**
     * Set meshConceptUid
     *
     * @param string $meshConceptUid
     * @return MeshConceptXTerm
     */
    public function setMeshConceptUid($meshConceptUid)
    {
        $this->meshConceptUid = $meshConceptUid;

        return $this;
    }

    /**
     * Get meshConceptUid
     *
     * @return string 
     */
    public function getMeshConceptUid()
    {
        return $this->meshConceptUid;
    }

    /**
     * Set meshTermUid
     *
     * @param string $meshTermUid
     * @return MeshConceptXTerm
     */
    public function setMeshTermUid($meshTermUid)
    {
        $this->meshTermUid = $meshTermUid;

        return $this;
    }

    /**
     * Get meshTermUid
     *
     * @return string 
     */
    public function getMeshTermUid()
    {
        return $this->meshTermUid;
    }
}
