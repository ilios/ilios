<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\ProgramYearDTO;

final class ProgramYearData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'startYear' => 2013,
            'locked' => false,
            'archived' => false,
            'program' => "1",
            'cohort' => "1",
            'directors' => ['1'],
            'competencies' => ['1', '3'],
            'terms' => [],
            'programYearObjectives' => ['1'],
        ];
        $arr[] = [
            'id' => 2,
            'startYear' => 2014,
            'locked' => false,
            'archived' => false,
            'program' => "1",
            'cohort' => "2",
            'directors' => [],
            'competencies' => [],
            'terms' => ['1', '4'],
            'programYearObjectives' => [],
        ];
        $arr[] = [
            'id' => 3,
            'startYear' => 2015,
            'locked' => false,
            'archived' => true,
            'program' => "2",
            'cohort' => "3",
            'directors' => [],
            'competencies' => [],
            'terms' => [],
            'programYearObjectives' => [],
        ];
        $arr[] = [
            'id' => 4,
            'startYear' => 2016,
            'locked' => true,
            'archived' => false,
            'program' => "3",
            'cohort' => "4",
            'directors' => [],
            'competencies' => [],
            'terms' => [],
            'programYearObjectives' => [],
        ];
        $arr[] = [
            'id' => 5,
            'startYear' => 2017,
            'locked' => false,
            'archived' => false,
            'program' => "1",
            'cohort' => "5",
            'directors' => [],
            'competencies' => [],
            'terms' => [],
            'programYearObjectives' => ['2'],
        ];

        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 6,
            'startYear' => 2015,
            'program' => "1",
            'directors' => [],
            'competencies' => [],
            'terms' => [],
            'programYearObjectives' => [],
            'locked' => false,
            'archived' => false,
        ];
    }

    public function createInvalid(): array
    {
        return [];
    }

    public function getDtoClass(): string
    {
        return ProgramYearDTO::class;
    }
}
