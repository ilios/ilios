<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\TermDTO;

final class TermData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];
        $arr[] = [
            'id' => 1,
            'title' => 'first term',
            'description' => 'some text',
            'vocabulary' => 1,
            'parent' => null,
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
            'description' => 'some description',
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
            'title' => 'third term',
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
            'title' => 'fourth term',
            'description' => 'lorem ipsum dolor et',
            'vocabulary' => 2,
            'parent' => null,
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
            'title' => 'fifth term',
            'description' => 'salt salt salt',
            'vocabulary' => 2,
            'parent' => null,
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
            'title' => 'sixth term',
            'description' => 'test description',
            'vocabulary' => 2,
            'parent' => null,
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

    public function create(): array
    {
        return [
            'id' => 7,
            'title' => 'seventh term',
            'description' => 'new description',
            'vocabulary' => 2,
            'parent' => null,
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

    public function createInvalid(): array
    {
        return [
            'vocabulary' => 11,
        ];
    }

    public function getDtoClass(): string
    {
        return TermDTO::class;
    }
}
