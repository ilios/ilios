<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\TitledNullableEntity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Attributes as IA;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;
use App\Repository\ReportRepository;
use Override;

#[ORM\Table(name: 'report')]
#[ORM\Entity(repositoryClass: ReportRepository::class)]
#[IA\Entity]
class Report implements ReportInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;
    use TitledNullableEntity;

    #[ORM\Column(name: 'report_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected int $id;

    #[ORM\Column(type: 'string', length: 240, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 240)]
    protected ?string $title = null;

    #[ORM\Column(name: 'creation_date', type: 'datetime')]
    #[IA\Expose]
    #[IA\OnlyReadable]
    #[IA\Type('dateTime')]
    #[Assert\NotBlank]
    protected DateTime $createdAt;

    #[ORM\ManyToOne(targetEntity: School::class)]
    #[ORM\JoinColumn(name: 'school_id', referencedColumnName: 'school_id')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected ?SchoolInterface $school = null;

    #[ORM\Column(name: 'subject', type: 'string', length: 32)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 32)]
    protected string $subject;

    #[ORM\Column(name: 'prepositional_object', type: 'string', length: 32, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 32)]
    protected ?string $prepositionalObject = null;

    #[ORM\Column(name: 'prepositional_object_table_row_id', type: 'string', length: 14, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(max: 14)]
    protected ?string $prepositionalObjectTableRowId = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'reports')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id', nullable: false, onDelete: 'cascade')]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected UserInterface $user;

    /**
     * Default createdAt to now
     */
    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    #[Override]
    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    #[Override]
    public function setSubject(string $subject): void
    {
        $this->subject = $subject;
    }

    #[Override]
    public function getSubject(): string
    {
        return $this->subject;
    }

    #[Override]
    public function setPrepositionalObject(?string $prepositionalObject): void
    {
        $this->prepositionalObject = $prepositionalObject;
    }

    #[Override]
    public function getPrepositionalObject(): ?string
    {
        return $this->prepositionalObject;
    }

    #[Override]
    public function setPrepositionalObjectTableRowId(?string $prepositionalObjectTableRowId): void
    {
        $this->prepositionalObjectTableRowId = $prepositionalObjectTableRowId;
    }

    #[Override]
    public function getPrepositionalObjectTableRowId(): ?string
    {
        return $this->prepositionalObjectTableRowId;
    }

    #[Override]
    public function setUser(UserInterface $user): void
    {
        $this->user = $user;
    }

    #[Override]
    public function getUser(): UserInterface
    {
        return $this->user;
    }

    #[Override]
    public function getSchool(): ?SchoolInterface
    {
        return $this->school;
    }

    #[Override]
    public function setSchool(?SchoolInterface $school = null): void
    {
        $this->school = $school;
    }
}
