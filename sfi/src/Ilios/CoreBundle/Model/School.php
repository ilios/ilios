<?php

namespace Ilios\CoreBundle\Model;



/**
 * School
 */
class School
{
    /**
     * @var int
     */
    protected $schoolId;

    /**
     * @var string
     */
    protected $templatePrefix;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $iliosAdministratorEmail;

    /**
     * @var boolean
     */
    protected $deleted;

    /**
     * @var string
     */
    protected $changeAlertRecipients;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $alerts;

    protected $competencies;

    protected $courses;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->alerts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->deleted = false;
    }

    /**
     * 
     * @return string
     */
    public function __toString()
    {
        return $this->title;
    }

    /**
     * Get schoolId
     *
     * @return int 
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
     * @param \Ilios\CoreBundle\Model\Alert $alerts
     * @return School
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
