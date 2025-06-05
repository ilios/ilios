<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\CurriculumInventoryInstitutionDTO;

final class CurriculumInventoryInstitutionData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];
        $arr[] = [
            'id' => 1,
            'name' => 'first institution',
            'aamcCode' => '1111',
            'addressStreet' => '221 West',
            'addressCity' => 'first city',
            'addressStateOrProvince' => 'AB',
            'addressZipCode' => '11111',
            'addressCountryCode' => 'UK',
            'school' => '1',
        ];
        $arr[] = [
            'id' => 2,
            'name' => 'second institution',
            'aamcCode' => "14",
            'addressStreet' => '221 East',
            'addressCity' => 'second city',
            'addressStateOrProvince' => 'CA',
            'addressZipCode' => '90210',
            'addressCountryCode' => 'BC',
            'school' => '2',
        ];

        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 3,
            'name' => 'third institution',
            'aamcCode' => '9494',
            'addressStreet' => '12 Main',
            'addressCity' => 'third city',
            'addressStateOrProvince' => 'XX',
            'addressZipCode' => '44332',
            'addressCountryCode' => 'US',
            'school' => '3',
        ];
    }

    public function createInvalid(): array
    {
        return [];
    }

    public function getDtoClass(): string
    {
        return CurriculumInventoryInstitutionDTO::class;
    }
}
