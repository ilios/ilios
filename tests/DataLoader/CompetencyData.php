<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\CompetencyDTO;

class CompetencyData extends AbstractDataLoader
{
    protected function getData()
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

    public function create()
    {
        return [
            'id' => 4,
            'title' => $this->faker->text(),
            'active' => true,
            'school' => "1",
            'parent' => "1",
            'children' => [],
            'aamcPcrses' => ['aamc-pcrs-comp-c0102'],
            'programYears' => ['1'],
            'programYearObjectives' => [],
        ];
    }

    public function createInvalid()
    {
        return [
            'school' => 11
        ];
    }

    public function getDtoClass(): string
    {
        return CompetencyDTO::class;
    }
}
