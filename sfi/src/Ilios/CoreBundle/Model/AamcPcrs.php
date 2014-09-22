<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * AamcPcrs
 */
class AamcPcrs
{
    /**
     * @var string
     */
    private $pcrsId;

    /**
     * @var string
     */
    private $description;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $competencies;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->competencies = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set pcrsId
     *
     * @param string $pcrsId
     * @return AamcPcrs
     */
    public function setPcrsId($pcrsId)
    {
        $this->pcrsId = $pcrsId;

        return $this;
    }

    /**
     * Get pcrsId
     *
     * @return string 
     */
    public function getPcrsId()
    {
        return $this->pcrsId;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return AamcPcrs
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Add competencies
     *
     * @param \Ilios\CoreBundle\Model\Competency $competencies
     * @return AamcPcrs
     */
    public function addCompetency(\Ilios\CoreBundle\Model\Competency $competencies)
    {
        $this->competencies[] = $competencies;

        return $this;
    }

    /**
     * Remove competencies
     *
     * @param \Ilios\CoreBundle\Model\Competency $competencies
     */
    public function removeCompetency(\Ilios\CoreBundle\Model\Competency $competencies)
    {
        $this->competencies->removeElement($competencies);
    }

    /**
     * Get competencies
     *
     * @return Ilios\CoreBundle\Model\Competency[]
     */
    public function getCompetencies()
    {
        return $this->competencies->toArray();
    }
}
