<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\TermDTO;

class TermData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = [];
        $arr[] = [
            'id' => 1,
            'title' => $this->faker->text(100),
            'description' => $this->faker->text(200),
            'vocabulary' => 1,
            'children' => ['2', '3'],
            'courses' => ['1', '2'],
            'programYears' => ["2"],
            'sessions' => ['2'],
            'aamcResourceTypes' => ['RE001'],
            'active' => true,
            'courseObjectives' => ['1', '2'],
            'programYearObjectives' => [],
            'sessionObjectives' => [],
        ];
        $arr[] = [
            'id' => 2,
            'title' => 'second term',
            'description' => $this->faker->text(200),
            'vocabulary' => 1,
            'parent' => 1,
            'children' => [],
            'courses' => [],
            'programYears' => [],
            'sessions' => ['1'],
            'aamcResourceTypes' => ['RE001', 'RE002'],
            'active' => true,
            'courseObjectives' => [],
            'programYearObjectives' => ['1', '2'],
            'sessionObjectives' => [],
        ];
        $arr[] = [
            'id' => 3,
            'title' => $this->faker->text(100),
            'description' => 'third description',
            'vocabulary' => 1,
            'parent' => 1,
            'children' => [],
            'courses' => ['4'],
            'programYears' => [],
            'sessions' => ['3'],
            'aamcResourceTypes' => ['RE002'],
            'active' => false,
            'courseObjectives' => [],
            'programYearObjectives' => [],
            'sessionObjectives' => ['1', '2'],
        ];

        $arr[] = [
            'id' => 4,
            'title' => $this->faker->text(100),
            'description' => $this->faker->text(200),
            'vocabulary' => 2,
            'children' => [],
            'courses' => ['2'],
            'programYears' => ["2"],
            'sessions' => ['2'],
            'aamcResourceTypes' => [],
            'active' => true,
            'courseObjectives' => ['1'],
            'programYearObjectives' => ['1'],
            'sessionObjectives' => ['1'],
        ];
        $arr[] = [
            'id' => 5,
            'title' => $this->faker->text(100),
            'description' => $this->faker->text(200),
            'vocabulary' => 2,
            'children' => [],
            'courses' => [],
            'programYears' => [],
            'sessions' => ['1'],
            'aamcResourceTypes' => [],
            'active' => true,
            'courseObjectives' => [],
            'programYearObjectives' => [],
            'sessionObjectives' => [],
        ];
        $arr[] = [
            'id' => 6,
            'title' => $this->faker->text(100),
            'description' => $this->faker->text(200),
            'vocabulary' => 2,
            'children' => [],
            'courses' => ['4'],
            'programYears' => [],
            'sessions' => ['3'],
            'aamcResourceTypes' => [],
            'active' => false,
            'courseObjectives' => [],
            'programYearObjectives' => [],
            'sessionObjectives' => [],
        ];

        return $arr;
    }

    public function create()
    {
        return [
            'id' => 7,
            'title' => $this->faker->text(100),
            'description' => $this->faker->text(200),
            'vocabulary' => 2,
            'children' => [],
            'courses' => ['4'],
            'programYears' => [],
            'sessions' => ['3'],
            'aamcResourceTypes' => [],
            'active' => true,
            'courseObjectives' => [],
            'programYearObjectives' => [],
            'sessionObjectives' => [],
        ];
    }

    public function createInvalid()
    {
        return [
            'vocabulary' => 11
        ];
    }

    public function getDtoClass(): string
    {
        return TermDTO::class;
    }
}
