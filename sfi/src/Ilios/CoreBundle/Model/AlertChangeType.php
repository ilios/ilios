<?php

namespace Ilios\CoreBundle\Model;



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
     * @param \Ilios\CoreBundle\Model\Alert $alerts
     * @return AlertChangeType
     */
    public function addAlert(\Ilios\CoreBundle\Model\Alert $alerts)
    {
        $this->alerts[] = $alerts;

        return $this;
    }

    /**
     * Remove alerts
     *
     * @param \Ilios\CoreBundle\Model\Alert $alerts
     */
    public function removeAlert(\Ilios\CoreBundle\Model\Alert $alerts)
    {
        $this->alerts->removeElement($alerts);
    }

    /**
     * Get alerts
     *
     * @return \Ilios\CoreBundle\Model\Alert[]
     */
    public function getAlerts()
    {
        return $this->alerts->toArray();
    }
}
