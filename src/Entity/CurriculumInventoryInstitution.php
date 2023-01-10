<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Attributes as IA;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\NameableEntity;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;
use App\Traits\SchoolEntity;
use App\Repository\CurriculumInventoryInstitutionRepository;

#[ORM\Table(name: 'curriculum_inventory_institution')]
#[ORM\Entity(repositoryClass: CurriculumInventoryInstitutionRepository::class)]
#[IA\Entity]
class CurriculumInventoryInstitution implements CurriculumInventoryInstitutionInterface
{
    use NameableEntity;
    use IdentifiableEntity;
    use StringableIdEntity;
    use SchoolEntity;

    #[ORM\Column(name: 'institution_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected int $id;

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

    #[ORM\OneToOne(inversedBy: 'curriculumInventoryInstitution', targetEntity: 'School')]
    #[ORM\JoinColumn(name: 'school_id', referencedColumnName: 'school_id', unique: true, nullable: false)]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected SchoolInterface $school;

    public function setAamcCode(string $aamcCode)
    {
        $this->aamcCode = $aamcCode;
    }

    public function getAamcCode(): string
    {
        return $this->aamcCode;
    }

    public function setAddressStreet(string $addressStreet)
    {
        $this->addressStreet = $addressStreet;
    }

    public function getAddressStreet(): string
    {
        return $this->addressStreet;
    }

    public function setAddressCity(string $addressCity)
    {
        $this->addressCity = $addressCity;
    }

    public function getAddressCity(): string
    {
        return $this->addressCity;
    }

    public function setAddressStateOrProvince(string $addressStateOrProvince)
    {
        $this->addressStateOrProvince = $addressStateOrProvince;
    }

    public function getAddressStateOrProvince(): string
    {
        return $this->addressStateOrProvince;
    }

    public function setAddressZipCode(string $addressZipcode)
    {
        $this->addressZipCode = $addressZipcode;
    }

    public function getAddressZipCode(): string
    {
        return $this->addressZipCode;
    }

    public function setAddressCountryCode(string $addressCountryCode)
    {
        $this->addressCountryCode = $addressCountryCode;
    }

    public function getAddressCountryCode(): string
    {
        return $this->addressCountryCode;
    }
}
