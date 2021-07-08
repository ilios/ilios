<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Annotation as IS;
use App\Repository\AlertRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;

/**
 * Class Alert
 * @IS\Entity
 */
#[ORM\Table(name: 'alert')]
#[ORM\Entity(repositoryClass: AlertRepository::class)]
class Alert implements AlertInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;
    /**
     * @var int
     * @Assert\Type(type="integer")
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    #[ORM\Column(name: 'alert_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected $id;
    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     * @IS\Expose
     * @IS\Type("integer")
     */
    #[ORM\Column(name: 'table_row_id', type: 'integer')]
    protected $tableRowId;
    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 30
     * )
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'table_name', type: 'string', length: 30)]
    protected $tableName;
    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=65000)
     * })
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'additional_text', type: 'text', nullable: true)]
    protected $additionalText;
    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     * @IS\Expose
     * @IS\Type("boolean")
     */
    #[ORM\Column(name: 'dispatched', type: 'boolean')]
    protected $dispatched;
    /**
     * @var ArrayCollection|AlertChangeTypeInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'AlertChangeType', inversedBy: 'alerts')]
    #[ORM\JoinTable(name: 'alert_change')]
    #[ORM\JoinColumn(name: 'alert_id', referencedColumnName: 'alert_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'alert_change_type_id', referencedColumnName: 'alert_change_type_id')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $changeTypes;
    /**
     * @var ArrayCollection|UserInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'alerts')]
    #[ORM\JoinTable(name: 'alert_instigator')]
    #[ORM\JoinColumn(name: 'alert_id', referencedColumnName: 'alert_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $instigators;
    /**
     * @var ArrayCollection|SchoolInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'School', inversedBy: 'alerts')]
    #[ORM\JoinTable(name: 'alert_recipient')]
    #[ORM\JoinColumn(name: 'alert_id', referencedColumnName: 'alert_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'school_id', referencedColumnName: 'school_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
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
