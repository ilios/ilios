<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\CurriculumInventoryReportDTO;

class CurriculumInventoryReportData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];

        $dt = $this->faker->dateTime();
        $dt->setTime(0, 0, 0);
        $arr[] = [
            'id' => 1,
            'program' => 1,
            'sequence' => 1,
            'year' => '2014',
            'name' => $this->faker->text(100),
            'description' => $this->faker->text(200),
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
            'year' => '2015',
            'name' => 'second report',
            'description' => $this->faker->text(200),
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
            'year' => '2016',
            'name' => $this->faker->text(100),
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
        $dt = $this->faker->dateTime();
        $dt->setTime(0, 0, 0);
        return [
            'id' => 4,
            'program' => 2,
            'year' => $this->faker->date('Y'),
            'name' => $this->faker->text(100),
            'description' => $this->faker->text(200),
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
