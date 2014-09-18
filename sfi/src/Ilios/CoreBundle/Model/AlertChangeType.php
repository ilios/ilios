<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AlertChangeType
 */
class AlertChangeType
{
    /**
     * @var integer
     */
    private $alertChangeTypeId;

    /**
     * @var string
     */
    private $title;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $alerts;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->alerts = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get alertChangeTypeId
     *
     * @return integer 
     */
    public function getAlertChangeTypeId()
    {
        return $this->alertChangeTypeId;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return AlertChangeType
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Add alerts
     *
     * @param \Ilios\CoreBundle\Entity\Alert $alerts
     * @return AlertChangeType
     */
    public function addAlert(\Ilios\CoreBundle\Entity\Alert $alerts)
    {
        $this->alerts[] = $alerts;

        return $this;
    }

    /**
     * Remove alerts
     *
     * @param \Ilios\CoreBundle\Entity\Alert $alerts
     */
    public function removeAlert(\Ilios\CoreBundle\Entity\Alert $alerts)
    {
        $this->alerts->removeElement($alerts);
    }

    /**
     * Get alerts
     *
     * @return \Ilios\CoreBundle\Entity\Alert[]
     */
    public function getAlerts()
    {
        return $this->alerts->toArray();
    }
}
