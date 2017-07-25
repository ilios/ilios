<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ilios\ApiBundle\Annotation as IS;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

/**
 * Class Alert
 *
 * @ORM\Table(name="alert")
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\AlertRepository")
 *
 * @IS\Entity
 */
class Alert implements AlertInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="alert_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
     *
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
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
     * @IS\Expose
     * @IS\Type("integer")
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
     * @IS\Expose
     * @IS\Type("string")
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
     * @IS\Expose
     * @IS\Type("string")
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
     * @IS\Expose
     * @IS\Type("boolean")
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
     * @IS\Expose
     * @IS\Type("entityCollection")
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
     * @IS\Expose
     * @IS\Type("entityCollection")
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
     * @IS\Expose
     * @IS\Type("entityCollection")
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
     * @inheritdoc
     */
    public function setTableRowId($tableRowId)
    {
        $this->tableRowId = $tableRowId;
    }

    /**
     * @inheritdoc
     */
    public function getTableRowId()
    {
        return $this->tableRowId;
    }

    /**
     * @inheritdoc
     */
    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    /**
     * @inheritdoc
     */
    public function getTableName()
    {
        return $this->tableName;
    }

    /**
     * @inheritdoc
     */
    public function setAdditionalText($additionalText)
    {
        $this->additionalText = $additionalText;
    }

    /**
     * @inheritdoc
     */
    public function getAdditionalText()
    {
        return $this->additionalText;
    }

    /**
     * @inheritdoc
     */
    public function setDispatched($dispatched)
    {
        $this->dispatched = $dispatched;
    }

    /**
     * @inheritdoc
     */
    public function isDispatched()
    {
        return $this->dispatched;
    }

    /**
     * @inheritdoc
     */
    public function setChangeTypes(Collection $changeTypes)
    {
        $this->changeTypes = new ArrayCollection();

        foreach ($changeTypes as $changeType) {
            $this->addChangeType($changeType);
        }
    }

    /**
     * @inheritdoc
     */
    public function addChangeType(AlertChangeTypeInterface $changeType)
    {
        if (!$this->changeTypes->contains($changeType)) {
            $this->changeTypes->add($changeType);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeChangeType(AlertChangeTypeInterface $changeType)
    {
        $this->changeTypes->removeElement($changeType);
    }

    /**
     * @inheritdoc
     */
    public function getChangeTypes()
    {
        return $this->changeTypes;
    }

    /**
     * @inheritdoc
     */
    public function setInstigators(Collection $instigators)
    {
        $this->instigators = new ArrayCollection();

        foreach ($instigators as $instigator) {
            $this->addInstigator($instigator);
        }
    }

    /**
     * @inheritdoc
     */
    public function addInstigator(UserInterface $instigator)
    {
        if (!$this->instigators->contains($instigator)) {
            $this->instigators->add($instigator);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeInstigator(UserInterface $instigator)
    {
        $this->instigators->removeElement($instigator);
    }

    /**
     * @inheritdoc
     */
    public function getInstigators()
    {
        return $this->instigators;
    }

    /**
     * @inheritdoc
     */
    public function setRecipients(Collection $recipients)
    {
        $this->recipients = new ArrayCollection();

        foreach ($recipients as $recipient) {
            $this->addRecipient($recipient);
        }
    }

    /**
     * @inheritdoc
     */
    public function addRecipient(SchoolInterface $recipient)
    {
        if (!$this->recipients->contains($recipient)) {
            $this->recipients->add($recipient);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeRecipient(SchoolInterface $recipient)
    {
        $this->recipients->removeElement($recipient);
    }

    /**
     * @inheritdoc
     */
    public function getRecipients()
    {
        return $this->recipients;
    }
}
