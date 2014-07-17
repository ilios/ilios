<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Report
 */
class Report
{
    /**
     * @var integer
     */
    private $reportId;

    /**
     * @var \DateTime
     */
    private $creationDate;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $prepositionalObject;

    /**
     * @var boolean
     */
    private $deleted;

    /**
     * @var string
     */
    private $title;

    /**
     * @var \Ilios\CoreBundle\Entity\User $user
     */
    private $user;

    /**
     * Get reportId
     *
     * @return integer 
     */
    public function getReportId()
    {
        return $this->reportId;
    }

    /**
     * Set creationDate
     *
     * @param \DateTime $creationDate
     * @return Report
     */
    public function setCreationDate($creationDate)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate
     *
     * @return \DateTime 
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set subject
     *
     * @param string $subject
     * @return Report
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string 
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set prepositionalObject
     *
     * @param string $prepositionalObject
     * @return Report
     */
    public function setPrepositionalObject($prepositionalObject)
    {
        $this->prepositionalObject = $prepositionalObject;

        return $this;
    }

    /**
     * Get prepositionalObject
     *
     * @return string 
     */
    public function getPrepositionalObject()
    {
        return $this->prepositionalObject;
    }

    /**
     * Set deleted
     *
     * @param boolean $deleted
     * @return Report
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
     * Set title
     *
     * @param string $title
     * @return Report
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
     * Set user
     *
     * @param \Ilios\CoreBundle\Entity\User $user
     * @return Report
     */
    public function setUser(\Ilios\CoreBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Ilios\CoreBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}
