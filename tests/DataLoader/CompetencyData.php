<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\CompetencyDTO;

final class CompetencyData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'title' => 'first competency',
            'active' => true,
            'school' => "1",
            'children' => ['3'],
            'aamcPcrses' => ['aamc-pcrs-comp-c0101'],
            'programYears' => ['1'],
            'programYearObjectives' => ['1'],
            'parent' => null,
        ];

        $arr[] = [
            'id' => 2,
            'title' => 'second competency',
            'active' => false,
            'school' => "1",
            'children' => [],
            'aamcPcrses' => ['aamc-pcrs-comp-c0101', 'aamc-pcrs-comp-c0102'],
            'programYears' => [],
            'programYearObjectives' => ['2'],
            'parent' => null,
        ];

        $arr[] = [
            'id' => 3,
            'title' => 'third competency',
            'active' => true,
            'school' => "1",
            'parent' => "1",
            'children' => [],
            'aamcPcrses' => ['aamc-pcrs-comp-c0102'],
            'programYears' => ['1'],
            'programYearObjectives' => [],
        ];

        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 4,
            'title' => 'fourth competency',
            'active' => true,
            'school' => "1",
            'parent' => "1",
            'children' => [],
            'aamcPcrses' => ['aamc-pcrs-comp-c0102'],
            'programYears' => ['1'],
            'programYearObjectives' => [],
        ];
    }

    public function createInvalid(): array
    {
        return [
            'school' => 11,
        ];
    }

    public function getDtoClass(): string
    {
        return CompetencyDTO::class;
    }
}
