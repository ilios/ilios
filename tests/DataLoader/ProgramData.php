<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\ProgramDTO;

final class ProgramData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'title' => 'first program',
            'shortTitle' => 'fp',
            'duration' => 3,
            'school' => "1",
            'programYears' => ["1", "2", "5"],
            'curriculumInventoryReports' => ["1", "2", "3"],
            'directors' => ['1'],
        ];

        $arr[] = [
            'id' => 2,
            'title' => 'second program',
            'shortTitle' => 'sp',
            'duration' => 4,
            'school' => "1",
            'programYears' => ["3"],
            'curriculumInventoryReports' => [],
            'directors' => [],
        ];

        $arr[] = [
            'id' => 3,
            'title' => 'third program',
            'shortTitle' => 'tp',
            'duration' => 4,
            'school' => "2",
            'programYears' => ["4"],
            'curriculumInventoryReports' => [],
            'directors' => [],
        ];


        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 4,
            'title' => 'fourth program',
            'shortTitle' => 'p4',
            'duration' => 4,
            'school' => "1",
            'programYears' => [],
            'curriculumInventoryReports' => [],
            'directors' => [],
        ];
    }

    public function createInvalid(): array
    {
        return [];
    }

    public function getDtoClass(): string
    {
        return ProgramDTO::class;
    }
}
