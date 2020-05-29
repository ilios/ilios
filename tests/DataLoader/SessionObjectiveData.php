<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\SessionObjectiveDTO;

class SessionObjectiveData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'position' => 0,
            'session' => '1',
            'objective' => '3',
            'terms' => ['3', '4'],
        ];

        $arr[] = [
            'id' => 2,
            'position' => 0,
            'session' => '4',
            'objective' => '6',
            'terms' => ['3'],
        ];

        $arr[] = [
            'id' => 3,
            'position' => 0,
            'session' => '4',
            'objective' => '7',
            'terms' => [],
        ];

        return $arr;
    }

    public function create()
    {
        return [
            'id' => 4,
            'position' => 0,
            'session' => '1',
            'objective' => '11',
            'terms' => [],
        ];
    }

    public function createInvalid()
    {
        return [];
    }

    public function createJsonApi(array $arr): object
    {
        $item = $this->buildJsonApiObject($arr, SessionObjectiveDTO::class);
        return json_decode(json_encode(['data' => $item]), false);
    }
}
