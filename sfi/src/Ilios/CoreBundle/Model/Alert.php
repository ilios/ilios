<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Alert
 */
class Alert
{
    /**
     * @var integer
     */
    private $alertId;

    /**
     * @var integer
     */
    private $tableRowId;

    /**
     * @var string
     */
    private $tableName;

    /**
     * @var string
     */
    private $additionalText;

    /**
     * @var boolean
     */
    private $dispatched;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $changeTypes;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $instigators;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $recipients;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->changeTypes = new \Doctrine\Common\Collections\ArrayCollection();
        $this->instigators = new \Doctrine\Common\Collections\ArrayCollection();
        $this->recipients = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get alertId
     *
     * @return integer 
     */
    public function getAlertId()
    {
        return $this->alertId;
    }

    /**
     * Set tableRowId
     *
     * @param integer $tableRowId
     * @return Alert
     */
    public function setTableRowId($tableRowId)
    {
        $this->tableRowId = $tableRowId;

        return $this;
    }

    /**
     * Get tableRowId
     *
     * @return integer 
     */
    public function getTableRowId()
    {
        return $this->tableRowId;
    }

    /**
     * Set tableName
     *
     * @param string $tableName
     * @return Alert
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;

        return $this;
    }

    /**
     * Get tableName
     *
     * @return string 
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * Set additionalText
     *
     * @param string $additionalText
     * @return Alert
     */
    public function setAdditionalText($additionalText)
    {
        $this->additionalText = $additionalText;

        return $this;
    }

    /**
     * Get additionalText
     *
     * @return string 
     */
    public function getAdditionalText()
    {
        return $this->additionalText;
    }

    /**
     * Set dispatched
     *
     * @param boolean $dispatched
     * @return Alert
     */
    public function setDispatched($dispatched)
    {
        $this->dispatched = $dispatched;

        return $this;
    }

    /**
     * Get dispatched
     *
     * @return boolean 
     */
    public function getDispatched()
    {
        return $this->dispatched;
    }

    /**
     * Add changeTypes
     *
     * @param \Ilios\CoreBundle\Model\AlertChangeType $changeTypes
     * @return Alert
     */
    public function addChangeType(\Ilios\CoreBundle\Model\AlertChangeType $changeTypes)
    {
        $this->changeTypes[] = $changeTypes;

        return $this;
    }

    /**
     * Remove changeTypes
     *
     * @param \Ilios\CoreBundle\Model\AlertChangeType $changeTypes
     */
    public function removeChangeType(\Ilios\CoreBundle\Model\AlertChangeType $changeTypes)
    {
        $this->changeTypes->removeElement($changeTypes);
    }

    /**
     * Get changeTypes
     *
     * @return \Ilios\CoreBundle\Model\AlertChangeType[]
     */
    public function getChangeTypes()
    {
        return $this->changeTypes->toArray();
    }

    /**
     * Add instigators
     *
     * @param \Ilios\CoreBundle\Model\User $instigators
     * @return Alert
     */
    public function addInstigator(\Ilios\CoreBundle\Model\User $instigators)
    {
        $this->instigators[] = $instigators;

        return $this;
    }

    /**
     * Remove instigators
     *
     * @param \Ilios\CoreBundle\Model\User $instigators
     */
    public function removeInstigator(\Ilios\CoreBundle\Model\User $instigators)
    {
        $this->instigators->removeElement($instigators);
    }

    /**
     * Get instigators
     *
     * @return \Ilios\CoreBundle\Model\User[]
     */
    public function getInstigators()
    {
        return $this->instigators->toArray();
    }

    /**
     * Add recipients
     *
     * @param \Ilios\CoreBundle\Model\School $recipients
     * @return Alert
     */
    public function addRecipient(\Ilios\CoreBundle\Model\School $recipients)
    {
        $this->recipients[] = $recipients;

        return $this;
    }

    /**
     * Remove recipients
     *
     * @param \Ilios\CoreBundle\Model\School $recipients
     */
    public function removeRecipient(\Ilios\CoreBundle\Model\School $recipients)
    {
        $this->recipients->removeElement($recipients);
    }

    /**
     * Get recipients
     *
     * @return \Ilios\CoreBundle\Model\School[]
     */
    public function getRecipients()
    {
        return $this->recipients->toArray();
    }
}
