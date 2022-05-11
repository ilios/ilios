<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\TitledNullableEntity;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Attribute as IA;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;
use App\Repository\ReportRepository;

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
    protected ?string $title;

    #[ORM\Column(name: 'creation_date', type: 'datetime')]
    #[IA\Expose]
    #[IA\OnlyReadable]
    #[IA\Type('dateTime')]
    #[Assert\NotBlank]
    protected DateTime $createdAt;

    #[ORM\ManyToOne(targetEntity: 'School')]
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

    #[ORM\ManyToOne(targetEntity: 'User', inversedBy: 'reports')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'cascade', nullable: false)]
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

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setSubject(string $subject)
    {
        $this->subject = $subject;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function setPrepositionalObject(?string $prepositionalObject)
    {
        $this->prepositionalObject = $prepositionalObject;
    }

    public function getPrepositionalObject(): ?string
    {
        return $this->prepositionalObject;
    }

    public function setPrepositionalObjectTableRowId(?string $prepositionalObjectTableRowId)
    {
        $this->prepositionalObjectTableRowId = $prepositionalObjectTableRowId;
    }

    public function getPrepositionalObjectTableRowId(): ?string
    {
        return $this->prepositionalObjectTableRowId;
    }

    public function setUser(UserInterface $user)
    {
        $this->user = $user;
    }

    public function getUser(): UserInterface
    {
        return $this->user;
    }

    public function getSchool(): ?SchoolInterface
    {
        return $this->school;
    }

    public function setSchool(SchoolInterface $school = null): void
    {
        $this->school = $school;
    }
}
