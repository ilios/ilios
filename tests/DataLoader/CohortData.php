<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\CohortDTO;

final class CohortData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'title' => "Class of 2017",
            'programYear' => 1,
            'courses' => ['1', '2'],
            'learnerGroups' => ['1', '3', '4', '5'],
            'users' => ['1', '2'],
        ];

        $arr[] = [
            'id' => 2,
            'title' => "Class of 2018",
            'programYear' => 2,
            'courses' => ["3"],
            'learnerGroups' => ['2'],
            'users' => [],
        ];

        $arr[] = [
            'id' => 3,
            'title' => "Class of 2019",
            'programYear' => 3,
            'courses' => ["4", "5"],
            'learnerGroups' => [],
            'users' => [],
        ];

        $arr[] = [
            'id' => 4,
            'title' => "Class of 2020",
            'programYear' => 4,
            'courses' => [],
            'learnerGroups' => [],
            'users' => [],
        ];

        $arr[] = [
            'id' => 5,
            'title' => "Class of 2021",
            'programYear' => 5,
            'courses' => [],
            'learnerGroups' => [],
            'users' => [],
        ];


        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 6,
            'title' => 'sixth cohort',
            'programYear' => "5",
            'courses' => ['1'],
            'learnerGroups' => [],
            'users' => ['1'],
        ];
    }

    public function createInvalid(): array
    {
        return [
            'id' => 'string',
            'title' => null,
            'programYear' => null,
        ];
    }

    public function getDtoClass(): string
    {
        return CohortDTO::class;
    }
}
