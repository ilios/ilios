<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AamcMethod
 */
class AamcMethod
{
    /**
     * @var string
     */
    private $methodId;

    /**
     * @var string
     */
    private $description;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $sessionTypes;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sessionTypes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set methodId
     *
     * @param string $methodId
     * @return AamcMethod
     */
    public function setMethodId($methodId)
    {
        $this->methodId = $methodId;

        return $this;
    }

    /**
     * Get methodId
     *
     * @return string 
     */
    public function getMethodId()
    {
        return $this->methodId;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return AamcMethod
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
     * Add sessionTypes
     *
     * @param \Ilios\CoreBundle\Entity\SessionType $sessionTypes
     * @return AamcMethod
     */
    public function addSessionType(\Ilios\CoreBundle\Entity\SessionType $sessionTypes)
    {
        $this->sessionTypes[] = $sessionTypes;

        return $this;
    }

    /**
     * Remove sessionTypes
     *
     * @param \Ilios\CoreBundle\Entity\SessionType $sessionTypes
     */
    public function removeSessionType(\Ilios\CoreBundle\Entity\SessionType $sessionTypes)
    {
        $this->sessionTypes->removeElement($sessionTypes);
    }

    /**
     * Get sessionTypes
     *
     * @return array[\Ilios\CoreBundle\Entity\SessionType]
     */
    public function getSessionTypes()
    {
        return $this->sessionTypes->toArray();
    }
}
