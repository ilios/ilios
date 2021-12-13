<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Attribute as IA;
use App\Repository\AlertRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;

/**
 * Class Alert
 */
#[ORM\Table(name: 'alert')]
#[ORM\Entity(repositoryClass: AlertRepository::class)]
#[IA\Entity]
class Alert implements AlertInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;

    /**
     * @var int
     * @Assert\Type(type="integer")
     */
    #[ORM\Column(name: 'alert_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\ReadOnly]
    protected $id;

    /**
     * @var int
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     */
    #[ORM\Column(name: 'table_row_id', type: 'integer')]
    #[IA\Expose]
    #[IA\Type('integer')]
    protected $tableRowId;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 30
     * )
     */
    #[ORM\Column(name: 'table_name', type: 'string', length: 30)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $tableName;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=65000)
     * })
     */
    #[ORM\Column(name: 'additional_text', type: 'text', nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $additionalText;

    /**
     * @var bool
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     */
    #[ORM\Column(name: 'dispatched', type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    protected $dispatched;

    /**
     * @var ArrayCollection|AlertChangeTypeInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'AlertChangeType', inversedBy: 'alerts')]
    #[ORM\JoinTable(name: 'alert_change')]
    #[ORM\JoinColumn(name: 'alert_id', referencedColumnName: 'alert_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'alert_change_type_id', referencedColumnName: 'alert_change_type_id')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $changeTypes;

    /**
     * @var ArrayCollection|UserInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'alerts')]
    #[ORM\JoinTable(name: 'alert_instigator')]
    #[ORM\JoinColumn(name: 'alert_id', referencedColumnName: 'alert_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $instigators;

    /**
     * @var ArrayCollection|SchoolInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'School', inversedBy: 'alerts')]
    #[ORM\JoinTable(name: 'alert_recipient')]
    #[ORM\JoinColumn(name: 'alert_id', referencedColumnName: 'alert_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'school_id', referencedColumnName: 'school_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $recipients;

    public function __construct()
    {
        $this->changeTypes = new ArrayCollection();
        $this->instigators = new ArrayCollection();
        $this->recipients = new ArrayCollection();
        $this->dispatched = false;
    }

    public function setTableRowId($tableRowId)
    {
        $this->tableRowId = $tableRowId;
    }

    public function getTableRowId(): int
    {
        return $this->tableRowId;
    }

    public function setTableName($tableName)
    {
        $this->tableName = $tableName;
    }

    public function getTableName(): string
    {
        return $this->tableName;
    }

    public function setAdditionalText($additionalText)
    {
        $this->additionalText = $additionalText;
    }

    public function getAdditionalText(): string
    {
        return $this->additionalText;
    }

    public function setDispatched($dispatched)
    {
        $this->dispatched = $dispatched;
    }

    public function isDispatched(): bool
    {
        return $this->dispatched;
    }

    public function setChangeTypes(Collection $changeTypes)
    {
        $this->changeTypes = new ArrayCollection();

        foreach ($changeTypes as $changeType) {
            $this->addChangeType($changeType);
        }
    }

    public function addChangeType(AlertChangeTypeInterface $changeType)
    {
        if (!$this->changeTypes->contains($changeType)) {
            $this->changeTypes->add($changeType);
        }
    }

    public function removeChangeType(AlertChangeTypeInterface $changeType)
    {
        $this->changeTypes->removeElement($changeType);
    }

    public function getChangeTypes(): Collection
    {
        return $this->changeTypes;
    }

    public function setInstigators(Collection $instigators)
    {
        $this->instigators = new ArrayCollection();

        foreach ($instigators as $instigator) {
            $this->addInstigator($instigator);
        }
    }

    public function addInstigator(UserInterface $instigator)
    {
        if (!$this->instigators->contains($instigator)) {
            $this->instigators->add($instigator);
        }
    }

    public function removeInstigator(UserInterface $instigator)
    {
        $this->instigators->removeElement($instigator);
    }

    public function getInstigators(): Collection
    {
        return $this->instigators;
    }

    public function setRecipients(Collection $recipients)
    {
        $this->recipients = new ArrayCollection();

        foreach ($recipients as $recipient) {
            $this->addRecipient($recipient);
        }
    }

    public function addRecipient(SchoolInterface $recipient)
    {
        if (!$this->recipients->contains($recipient)) {
            $this->recipients->add($recipient);
        }
    }

    public function removeRecipient(SchoolInterface $recipient)
    {
        $this->recipients->removeElement($recipient);
    }

    public function getRecipients(): Collection
    {
        return $this->recipients;
    }
}
