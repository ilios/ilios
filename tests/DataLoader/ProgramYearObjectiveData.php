<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\ProgramYearObjectiveDTO;

class ProgramYearObjectiveData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'position' => 0,
            'programYear' => '1',
            'objective' => '1',
            'terms' => ['2', '4'],
        ];

        $arr[] = [
            'id' => 2,
            'position' => 0,
            'programYear' => '1',
            'objective' => '2',
            'terms' => ['2'],
        ];

        $arr[] = [
            'id' => 3,
            'position' => 0,
            'programYear' => '5',
            'objective' => '8',
            'terms' => [],
        ];

        return $arr;
    }

    public function create()
    {
        return [
            'id' => 4,
            'position' => 0,
            'programYear' => '2',
            'objective' => '9',
            'terms' => [],
        ];
    }

    public function createInvalid()
    {
        return [];
    }

    public function createJsonApi(array $arr): object
    {
        $item = $this->buildJsonApiObject($arr, ProgramYearObjectiveDTO::class);
        return json_decode(json_encode(['data' => $item]), false);
    }
}
