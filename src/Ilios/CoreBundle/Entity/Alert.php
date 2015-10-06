<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

/**
 * Class Alert
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="alert")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessType("public_method")
 */
class Alert implements AlertInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;

    /**
     * @deprecated Replace with trait.
     * @var int
     *
     * @ORM\Column(name="alert_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    protected $id;

    /**
     * @var int
     *
     * @ORM\Column(name="table_row_id", type="integer")
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     * @JMS\SerializedName("tableRowId")
     */
    protected $tableRowId;

    /**
     * @var string
     *
     * @ORM\Column(name="table_name", type="string", length=30)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 30
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("tableName")
     */
    protected $tableName;

    /**
     * @var string
     *
     * @ORM\Column(name="additional_text", type="text", nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 65000
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("additionalText")
     */
    protected $additionalText;

    /**
     * @var boolean
     *
     * @ORM\Column(name="dispatched", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $dispatched;

    /**
     * @var ArrayCollection|AlertChangeTypeInterface[]
     *
     * @ORM\ManyToMany(targetEntity="AlertChangeType", inversedBy="alerts")
     * @ORM\JoinTable(name="alert_change",
     *   joinColumns={
     *     @ORM\JoinColumn(name="alert_id", referencedColumnName="alert_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="alert_change_type_id", referencedColumnName="alert_change_type_id")
     *   }
     * )
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("changeTypes")
     */
    protected $changeTypes;

    /**
     * @var ArrayCollection|UserInterface[]
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="alerts")
     * @ORM\JoinTable(name="alert_instigator",
     *   joinColumns={
     *     @ORM\JoinColumn(name="alert_id", referencedColumnName="alert_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", onDelete="CASCADE")
     *   }
     * )
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $instigators;

    /**
     * @var ArrayCollection|SchoolInterface[]
     *
     * @ORM\ManyToMany(targetEntity="School", inversedBy="alerts")
     * @ORM\JoinTable(name="alert_recipient",
     *   joinColumns={
     *     @ORM\JoinColumn(name="alert_id", referencedColumnName="alert_id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="school_id", referencedColumnName="school_id")
     *   }
     * )
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $recipients;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->changeTypes = new ArrayCollection();
        $this->instigators = new ArrayCollection();
        $this->recipients = new ArrayCollection();
        $this->dispatched = false;
    }

    /**
     * @param int $tableRowId
     */
    public function setTableRowId($tableRowId)
    {
        $this->tableRowId = $tableRowId;
    }

    /**
     * @return int
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
    public function isDispatched()
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
     * @param AlertChangeTypeInterface $changeType
     */
    public function addChangeType(AlertChangeTypeInterface $changeType)
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
