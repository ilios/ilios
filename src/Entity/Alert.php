<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Attributes as IA;
use App\Repository\AlertRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;
use Override;

#[ORM\Table(name: 'alert')]
#[ORM\Entity(repositoryClass: AlertRepository::class)]
#[IA\Entity]
class Alert implements AlertInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;

    #[ORM\Column(name: 'alert_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected int $id;

    #[ORM\Column(name: 'table_row_id', type: 'integer')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'integer')]
    protected int $tableRowId;

    #[ORM\Column(name: 'table_name', type: 'string', length: 30)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 30)]
    protected string $tableName;

    #[ORM\Column(name: 'additional_text', type: 'text', nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 65000)]
    protected ?string $additionalText = null;

    #[ORM\Column(name: 'dispatched', type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    #[Assert\NotNull]
    #[Assert\Type(type: 'bool')]
    protected bool $dispatched;

    #[ORM\ManyToMany(targetEntity: AlertChangeType::class, inversedBy: 'alerts')]
    #[ORM\JoinTable(name: 'alert_change')]
    #[ORM\JoinColumn(name: 'alert_id', referencedColumnName: 'alert_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'alert_change_type_id', referencedColumnName: 'alert_change_type_id')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $changeTypes;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'alerts')]
    #[ORM\JoinTable(name: 'alert_instigator')]
    #[ORM\JoinColumn(name: 'alert_id', referencedColumnName: 'alert_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $instigators;

    #[ORM\ManyToMany(targetEntity: School::class, inversedBy: 'alerts')]
    #[ORM\JoinTable(name: 'alert_recipient')]
    #[ORM\JoinColumn(name: 'alert_id', referencedColumnName: 'alert_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'school_id', referencedColumnName: 'school_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $recipients;

    #[ORM\ManyToMany(targetEntity: ServiceToken::class, inversedBy: 'alerts')]
    #[ORM\JoinTable(name: 'alert_service_token_instigators')]
    #[ORM\JoinColumn(name: 'alert_id', referencedColumnName: 'alert_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'service_token_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $serviceTokenInstigators;

    public function __construct()
    {
        $this->changeTypes = new ArrayCollection();
        $this->instigators = new ArrayCollection();
        $this->serviceTokenInstigators = new ArrayCollection();
        $this->recipients = new ArrayCollection();
        $this->dispatched = false;
    }

    #[Override]
    public function setTableRowId(int $tableRowId): void
    {
        $this->tableRowId = $tableRowId;
    }

    #[Override]
    public function getTableRowId(): int
    {
        return $this->tableRowId;
    }

    #[Override]
    public function setTableName(string $tableName): void
    {
        $this->tableName = $tableName;
    }

    #[Override]
    public function getTableName(): string
    {
        return $this->tableName;
    }

    #[Override]
    public function setAdditionalText(?string $additionalText): void
    {
        $this->additionalText = $additionalText;
    }

    #[Override]
    public function getAdditionalText(): ?string
    {
        return $this->additionalText;
    }

    #[Override]
    public function setDispatched(bool $dispatched): void
    {
        $this->dispatched = $dispatched;
    }

    #[Override]
    public function isDispatched(): bool
    {
        return $this->dispatched;
    }

    #[Override]
    public function setChangeTypes(Collection $changeTypes): void
    {
        $this->changeTypes = new ArrayCollection();

        foreach ($changeTypes as $changeType) {
            $this->addChangeType($changeType);
        }
    }

    #[Override]
    public function addChangeType(AlertChangeTypeInterface $changeType): void
    {
        if (!$this->changeTypes->contains($changeType)) {
            $this->changeTypes->add($changeType);
        }
    }

    #[Override]
    public function removeChangeType(AlertChangeTypeInterface $changeType): void
    {
        $this->changeTypes->removeElement($changeType);
    }

    #[Override]
    public function getChangeTypes(): Collection
    {
        return $this->changeTypes;
    }

    #[Override]
    public function setInstigators(Collection $instigators): void
    {
        $this->instigators = new ArrayCollection();

        foreach ($instigators as $instigator) {
            $this->addInstigator($instigator);
        }
    }

    #[Override]
    public function addInstigator(UserInterface $instigator): void
    {
        if (!$this->instigators->contains($instigator)) {
            $this->instigators->add($instigator);
        }
    }

    #[Override]
    public function removeInstigator(UserInterface $instigator): void
    {
        $this->instigators->removeElement($instigator);
    }

    #[Override]
    public function getInstigators(): Collection
    {
        return $this->instigators;
    }

    #[Override]
    public function setRecipients(Collection $recipients): void
    {
        $this->recipients = new ArrayCollection();

        foreach ($recipients as $recipient) {
            $this->addRecipient($recipient);
        }
    }

    #[Override]
    public function addRecipient(SchoolInterface $recipient): void
    {
        if (!$this->recipients->contains($recipient)) {
            $this->recipients->add($recipient);
        }
    }

    #[Override]
    public function removeRecipient(SchoolInterface $recipient): void
    {
        $this->recipients->removeElement($recipient);
    }

    #[Override]
    public function getRecipients(): Collection
    {
        return $this->recipients;
    }

    #[Override]
    public function setServiceTokenInstigators(Collection $instigators): void
    {
        $this->serviceTokenInstigators = new ArrayCollection();

        foreach ($instigators as $instigator) {
            $this->addServiceTokenInstigator($instigator);
        }
    }

    #[Override]
    public function addServiceTokenInstigator(ServiceTokenInterface $instigator): void
    {
        if (!$this->serviceTokenInstigators->contains($instigator)) {
            $this->serviceTokenInstigators->add($instigator);
        }
    }

    #[Override]
    public function removeServiceTokenInstigator(ServiceTokenInterface $instigator): void
    {
        $this->serviceTokenInstigators->removeElement($instigator);
    }

    #[Override]
    public function getServiceTokenInstigators(): Collection
    {
        return $this->serviceTokenInstigators;
    }
}
