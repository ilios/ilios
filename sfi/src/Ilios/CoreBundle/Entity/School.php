<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * School
 */
class School
{
    /**
     * @var integer
     */
    private $schoolId;

    /**
     * @var string
     */
    private $templatePrefix;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $iliosAdministratorEmail;

    /**
     * @var boolean
     */
    private $deleted;

    /**
     * @var string
     */
    private $changeAlertRecipients;

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
     * Get schoolId
     *
     * @return integer 
     */
    public function getSchoolId()
    {
        return $this->schoolId;
    }

    /**
     * Set templatePrefix
     *
     * @param string $templatePrefix
     * @return School
     */
    public function setTemplatePrefix($templatePrefix)
    {
        $this->templatePrefix = $templatePrefix;

        return $this;
    }

    /**
     * Get templatePrefix
     *
     * @return string 
     */
    public function getTemplatePrefix()
    {
        return $this->templatePrefix;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return School
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
     * Set iliosAdministratorEmail
     *
     * @param string $iliosAdministratorEmail
     * @return School
     */
    public function setIliosAdministratorEmail($iliosAdministratorEmail)
    {
        $this->iliosAdministratorEmail = $iliosAdministratorEmail;

        return $this;
    }

    /**
     * Get iliosAdministratorEmail
     *
     * @return string 
     */
    public function getIliosAdministratorEmail()
    {
        return $this->iliosAdministratorEmail;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return School
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;

        return $this;
    }

    /**
     * Get deleted
     *
     * @return boolean 
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set changeAlertRecipients
     *
     * @param string $changeAlertRecipients
     * @return School
     */
    public function setChangeAlertRecipients($changeAlertRecipients)
    {
        $this->changeAlertRecipients = $changeAlertRecipients;

        return $this;
    }

    /**
     * Get changeAlertRecipients
     *
     * @return string 
     */
    public function getChangeAlertRecipients()
    {
        return $this->changeAlertRecipients;
    }

    /**
     * Add alerts
     *
     * @param \Ilios\CoreBundle\Entity\Alert $alerts
     * @return School
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
