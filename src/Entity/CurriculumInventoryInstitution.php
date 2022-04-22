<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Attribute as IA;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\NameableEntity;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;
use App\Traits\SchoolEntity;
use App\Repository\CurriculumInventoryInstitutionRepository;

/**
 * Class CurriculumInventoryInstitution
 */
#[ORM\Table(name: 'curriculum_inventory_institution')]
#[ORM\Entity(repositoryClass: CurriculumInventoryInstitutionRepository::class)]
#[IA\Entity]
class CurriculumInventoryInstitution implements CurriculumInventoryInstitutionInterface
{
    use NameableEntity;
    use IdentifiableEntity;
    use StringableIdEntity;
    use SchoolEntity;

    /**
     * @var int
     */
    #[ORM\Column(name: 'institution_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected $id;

    /**
     * @var string
     */
    #[ORM\Column(type: 'string', length: 100)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 100)]
    protected $name;

    /**
     * @var string
     */
    #[ORM\Column(name: 'aamc_code', type: 'string', length: 10)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 10)]
    protected $aamcCode;

    /**
     * @var string
     */
    #[ORM\Column(name: 'address_street', type: 'string', length: 100)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 100)]
    protected $addressStreet;

    /**
     * @var string
     */
    #[ORM\Column(name: 'address_city', type: 'string', length: 100)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 100)]
    protected $addressCity;

    /**
     * @var string
     */
    #[ORM\Column(name: 'address_state_or_province', type: 'string', length: 50)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 50)]
    protected $addressStateOrProvince;

    /**
     * @var string
     */
    #[ORM\Column(name: 'address_zipcode', type: 'string', length: 10)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 10)]
    protected $addressZipCode;

    /**
     * @var string
     */
    #[ORM\Column(name: 'address_country_code', type: 'string', length: 2)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 2)]
    protected $addressCountryCode;

    /**
     * @var SchoolInterface
     */
    #[ORM\OneToOne(inversedBy: 'curriculumInventoryInstitution', targetEntity: 'School')]
    #[ORM\JoinColumn(name: 'school_id', referencedColumnName: 'school_id', unique: true, nullable: false)]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected $school;

    /**
     * @param string $aamcCode
     */
    public function setAamcCode($aamcCode)
    {
        $this->aamcCode = $aamcCode;
    }

    public function getAamcCode(): string
    {
        return $this->aamcCode;
    }

    /**
     * @param string $addressStreet
     */
    public function setAddressStreet($addressStreet)
    {
        $this->addressStreet = $addressStreet;
    }

    public function getAddressStreet(): string
    {
        return $this->addressStreet;
    }

    /**
     * @param string $addressCity
     */
    public function setAddressCity($addressCity)
    {
        $this->addressCity = $addressCity;
    }

    public function getAddressCity(): string
    {
        return $this->addressCity;
    }

    /**
     * @param string $addressStateOrProvince
     */
    public function setAddressStateOrProvince($addressStateOrProvince)
    {
        $this->addressStateOrProvince = $addressStateOrProvince;
    }

    public function getAddressStateOrProvince(): string
    {
        return $this->addressStateOrProvince;
    }

    /**
     * @param string $addressZipCode
     */
    public function setAddressZipCode($addressZipCode)
    {
        $this->addressZipCode = $addressZipCode;
    }

    public function getAddressZipCode(): string
    {
        return $this->addressZipCode;
    }

    /**
     * @param string $addressCountryCode
     */
    public function setAddressCountryCode($addressCountryCode)
    {
        $this->addressCountryCode = $addressCountryCode;
    }

    public function getAddressCountryCode(): string
    {
        return $this->addressCountryCode;
    }
}
