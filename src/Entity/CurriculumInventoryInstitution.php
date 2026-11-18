<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Attributes as IA;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\StringableIdEntity;
use App\Repository\CurriculumInventoryInstitutionRepository;
use Override;

#[ORM\Table(name: 'curriculum_inventory_institution')]
#[ORM\Entity(repositoryClass: CurriculumInventoryInstitutionRepository::class)]
#[IA\Entity]
class CurriculumInventoryInstitution implements CurriculumInventoryInstitutionInterface
{
    use StringableIdEntity;

    #[ORM\Column(name: 'institution_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected ?int $id;

    #[ORM\Column(type: 'string', length: 100)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 100)]
    protected string $name;

    #[ORM\Column(name: 'aamc_code', type: 'string', length: 10)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 10)]
    protected string $aamcCode;

    #[ORM\Column(name: 'address_street', type: 'string', length: 100)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 100)]
    protected string $addressStreet;

    #[ORM\Column(name: 'address_city', type: 'string', length: 100)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 100)]
    protected string $addressCity;

    #[ORM\Column(name: 'address_state_or_province', type: 'string', length: 50)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 50)]
    protected string $addressStateOrProvince;

    #[ORM\Column(name: 'address_zipcode', type: 'string', length: 10)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 10)]
    protected string $addressZipCode;

    #[ORM\Column(name: 'address_country_code', type: 'string', length: 2)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 2)]
    protected string $addressCountryCode;

    #[ORM\OneToOne(inversedBy: 'curriculumInventoryInstitution', targetEntity: School::class)]
    #[ORM\JoinColumn(name: 'school_id', referencedColumnName: 'school_id', unique: true, nullable: false)]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected SchoolInterface $school;

    #[Override]
    public function setAamcCode(string $aamcCode): void
    {
        $this->aamcCode = $aamcCode;
    }

    #[Override]
    public function getAamcCode(): string
    {
        return $this->aamcCode;
    }

    #[Override]
    public function setAddressStreet(string $addressStreet): void
    {
        $this->addressStreet = $addressStreet;
    }

    #[Override]
    public function getAddressStreet(): string
    {
        return $this->addressStreet;
    }

    #[Override]
    public function setAddressCity(string $addressCity): void
    {
        $this->addressCity = $addressCity;
    }

    #[Override]
    public function getAddressCity(): string
    {
        return $this->addressCity;
    }

    #[Override]
    public function setAddressStateOrProvince(string $addressStateOrProvince): void
    {
        $this->addressStateOrProvince = $addressStateOrProvince;
    }

    #[Override]
    public function getAddressStateOrProvince(): string
    {
        return $this->addressStateOrProvince;
    }

    #[Override]
    public function setAddressZipCode(string $addressZipcode): void
    {
        $this->addressZipCode = $addressZipcode;
    }

    #[Override]
    public function getAddressZipCode(): string
    {
        return $this->addressZipCode;
    }

    #[Override]
    public function setAddressCountryCode(string $addressCountryCode): void
    {
        $this->addressCountryCode = $addressCountryCode;
    }

    #[Override]
    public function getAddressCountryCode(): string
    {
        return $this->addressCountryCode;
    }
    #[Override]
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    #[Override]
    public function getId(): ?int
    {
        return $this->id;
    }

    #[Override]
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    #[Override]
    public function getName(): string
    {
        return $this->name;
    }

    #[Override]
    public function getSchool(): SchoolInterface
    {
        return $this->school;
    }

    #[Override]
    public function setSchool(SchoolInterface $school): void
    {
        $this->school = $school;
    }
}
