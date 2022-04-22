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

/**
 * Class Report
 */
#[ORM\Table(name: 'report')]
#[ORM\Entity(repositoryClass: ReportRepository::class)]
#[IA\Entity]
class Report implements ReportInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;
    use TitledNullableEntity;

    /**
     * @var int
     * @Assert\Type(type="integer")
     */
    #[ORM\Column(name: 'report_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    protected $id;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=240)
     * })
     */
    #[ORM\Column(type: 'string', length: 240, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $title;

    /**
     * @var DateTime
     * @Assert\NotBlank()
     */
    #[ORM\Column(name: 'creation_date', type: 'datetime')]
    #[IA\Expose]
    #[IA\OnlyReadable]
    #[IA\Type('dateTime')]
    protected $createdAt;

    /**
     * @var SchoolInterface
     */
    #[ORM\ManyToOne(targetEntity: 'School')]
    #[ORM\JoinColumn(name: 'school_id', referencedColumnName: 'school_id')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected $school;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 32
     * )
     */
    #[ORM\Column(name: 'subject', type: 'string', length: 32)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $subject;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=32)
     * })
     */
    #[ORM\Column(name: 'prepositional_object', type: 'string', length: 32, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $prepositionalObject;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=14)
     * })
     */
    #[ORM\Column(name: 'prepositional_object_table_row_id', type: 'string', length: 14, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $prepositionalObjectTableRowId;

    /**
     * @var UserInterface $user
     * @Assert\NotNull()
     */
    #[ORM\ManyToOne(targetEntity: 'User', inversedBy: 'reports')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'cascade', nullable: false)]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected $user;

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

    /**
     * @param string $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    /**
     * @param string $prepositionalObject
     */
    public function setPrepositionalObject($prepositionalObject)
    {
        $this->prepositionalObject = $prepositionalObject;
    }

    public function getPrepositionalObject(): ?string
    {
        return $this->prepositionalObject;
    }

    /**
     * @param string $prepositionalObjectTableRowId
     */
    public function setPrepositionalObjectTableRowId($prepositionalObjectTableRowId)
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
