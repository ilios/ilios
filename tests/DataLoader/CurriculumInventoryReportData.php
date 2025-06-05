<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\CurriculumInventoryReportDTO;
use DateTime;

final class CurriculumInventoryReportData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];

        $dt = new DateTime();
        $dt->setTime(0, 0, 0);
        $arr[] = [
            'id' => 1,
            'export' => null,
            'program' => 1,
            'sequence' => 1,
            'year' => 2014,
            'name' => 'first report',
            'description' => 'no description',
            'startDate' => $dt->format('c'),
            'endDate' => $dt->format('c'),
            'sequenceBlocks' => ['1', '2', '3', '4', '5'],
            'academicLevels' => ['1', '2', '3'],
            'administrators' => ['1'],
        ];

        $arr[] = [
            'id' => 2,
            'export' => 1,
            'program' => 1,
            'sequence' => 2,
            'year' => 2015,
            'name' => 'second report',
            'description' => 'something else',
            'startDate' => $dt->format('c'),
            'endDate' => $dt->format('c'),
            'sequenceBlocks' => [],
            'academicLevels' => [],
            'administrators' => [],
        ];
        $arr[] = [
            'id' => 3,
            'export' => 2,
            'program' => 1,
            'sequence' => null,
            'year' => 2016,
            'name' => 'third report',
            'description' => 'third report',
            'startDate' => $dt->format('c'),
            'endDate' => $dt->format('c'),
            'sequenceBlocks' => [],
            'academicLevels' => [],
            'administrators' => [],
        ];

        return $arr;
    }

    public function create(): array
    {
        $dt = new DateTime();
        $dt->setTime(0, 0, 0);
        return [
            'id' => 4,
            'program' => 2,
            'sequence' => null,
            'year' => 2021,
            'name' => 'fourth report',
            'description' => 'lirum larum loeffelstiel',
            'startDate' => $dt->format('c'),
            'endDate' => $dt->format('c'),
            'sequenceBlocks' => [],
            'academicLevels' => [],
            'administrators' => [],
        ];
    }

    public function createInvalid(): array
    {
        return [];
    }

    public function getDtoClass(): string
    {
        return CurriculumInventoryReportDTO::class;
    }
}
