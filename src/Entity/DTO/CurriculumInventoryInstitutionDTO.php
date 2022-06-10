<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;
use OpenApi\Attributes as OA;

#[IA\DTO('curriculumInventoryInstitutions')]
#[IA\ExposeGraphQL]
#[OA\Schema(
    title: "CurriculumInventoryInstitution",
    properties: [
        new OA\Property(
            "id",
            description: "ID",
            type: "integer"
        ),
        new OA\Property(
            "name",
            description: "Name",
            type: "string"
        ),
        new OA\Property(
            "aamcCode",
            description: "AAMC code",
            type: "string"
        ),
        new OA\Property(
            "addressStreet",
            description: "Street address",
            type: "string"
        ),
        new OA\Property(
            "addressCity",
            description: "City",
            type: "string"
        ),
        new OA\Property(
            "addressStateOrProvince",
            description: "State or province",
            type: "string"
        ),
        new OA\Property(
            "addressZipCode",
            description: "ZIP code",
            type: "string"
        ),
        new OA\Property(
            "addressCountryCode",
            description: "Country code",
            type: "string"
        ),
        new OA\Property(
            "school",
            description: "School",
            type: "string"
        ),
    ]
)]
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
