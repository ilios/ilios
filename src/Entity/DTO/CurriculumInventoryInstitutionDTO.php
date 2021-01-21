<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class CurriculumInventoryInstitutionDTO
 *
 * @IS\DTO("curriculumInventoryInstitutions")
 */
class CurriculumInventoryInstitutionDTO
{
    /**
     * @IS\Id
     * @IS\Expose
     * @IS\Type("integer")
     */
    public int $id;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public string $name;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public string $aamcCode;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public string $addressStreet;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public string $addressCity;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public string $addressStateOrProvince;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public string $addressZipCode;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public string $addressCountryCode;

    /**
     * @IS\Expose
     * @IS\Related("schools")
     * @IS\Type("string")
     */
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
