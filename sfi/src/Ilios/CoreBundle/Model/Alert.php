<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\CoreBundle\Traits\IdentifiableTrait;
use Symfony\Component\Validator\Constraints\Collection;

use Ilios\CoreBundle\Model\AlertChangeTypeInterface;
use Ilios\CoreBundle\Model\UserInterface;
use Ilios\CoreBundle\Model\SchoolInterface;

/**
 * Alert
 */
class Alert implements AlertInterface
{
    use IdentifiableTrait;

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
     * @var ArrayCollection|AlertChangeTypeInterface[]
     */
    private $changeTypes;

    /**
     * @var ArrayCollection|UserInterface[]
     */
    private $instigators;

    /**
     * @var ArrayCollection|SchoolInterface[]
     */
    private $recipients;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->changeTypes = new ArrayCollection();
        $this->instigators = new ArrayCollection();
        $this->recipients = new ArrayCollection();
    }

    /**
     * @param integer $tableRowId
     */
    public function setTableRowId($tableRowId)
    {
        $this->tableRowId = $tableRowId;
    }

    /**
     * @return integer
     */
    public function getTableRowId()
    {
        return $this->tableRowId;
    }

    /**
     * @param string $tableName
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * @return string
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @param string $additionalText
     */
    public function setAdditionalText($additionalText)
    {
        $this->additionalText = $additionalText;
    }

    /**
     * @return string
     */
    public function getAdditionalText()
    {
        return $this->additionalText;
    }

    /**
     * @param boolean $dispatched
     */
    public function setDispatched($dispatched)
    {
        $this->dispatched = $dispatched;
    }

    /**
     * @return boolean
     */
    public function getDispatched()
    {
        return $this->dispatched;
    }

    /**
     * @param Collection $changeTypes
     */
    public function setChangeTypes(Collection $changeTypes)
    {
        $this->changeTypes = new ArrayCollection();

        foreach ($changeTypes as $changeType) {
            $this->addChangeType($changeType);
        }
    }

    /**
     * @param AlertChangeType $changeType
     */
    public function addChangeType(AlertChangeType $changeType)
    {
        $this->changeTypes->add($changeType);
    }

    /**
     * @return ArrayCollection|AlertChangeTypeInterface[]
     */
    public function getChangeTypes()
    {
        return $this->changeTypes;
    }

    /**
     * @param Collection $instigators
     */
    public function setInstigators(Collection $instigators)
    {
        $this->instigators = new ArrayCollection();

        foreach ($instigators as $instigator) {
            $this->addInstigator($instigator);
        }
    }

    /**
     * @param UserInterface $instigator
     */
    public function addInstigator(UserInterface $instigator)
    {
        $this->instigators->add($instigator);
    }

    /**
     * @return ArrayCollection|UserInterface[]
     */
    public function getInstigators()
    {
        return $this->instigators;
    }

    /**
     * @param Collection $recipients
     */
    public function setRecipients(Collection $recipients)
    {
        $this->recipients = new ArrayCollection();

        foreach ($recipients as $recipient) {
            $this->addRecipient($recipient);
        }
    }

    /**
     * @param SchoolInterface $recipient
     */
    public function addRecipient(SchoolInterface $recipient)
    {
        $this->recipients->add($recipient);
    }

    /**
     * @return ArrayCollection|SchoolInterface[]
     */
    public function getRecipients()
    {
        return $this->recipients;
    }
}
