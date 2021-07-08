<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;

/**
 * Class CurriculumInventoryInstitutionDTO
 */
#[IA\DTO('curriculumInventoryInstitutions')]
class CurriculumInventoryInstitutionDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $name;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $aamcCode;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $addressStreet;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $addressCity;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $addressStateOrProvince;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $addressZipCode;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $addressCountryCode;

    #[IA\Expose]
    #[IA\Related('schools')]
    #[IA\Type('integer')]
    public int $school;

    public function __construct(
        int $id,
        string $name,
        string $aamcCode,
        string $addressStreet,
        string $addressCity,
        string $addressStateOrProvince,
        string $addressZipCode,
        string $addressCountryCode
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->aamcCode = $aamcCode;
        $this->addressStreet = $addressStreet;
        $this->addressCity = $addressCity;
        $this->addressStateOrProvince = $addressStateOrProvince;
        $this->addressZipCode = $addressZipCode;
        $this->addressCountryCode = $addressCountryCode;
    }
}
