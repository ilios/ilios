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
            'active' => true,
            'title' => 'session objective 1',
            'session' => '1',
            'terms' => ['3', '4'],
            'meshDescriptors' => [],
            'courseObjectives' => [],
            'descendants' => []
        ];

        $arr[] = [
            'id' => 2,
            'position' => 0,
            'active' => true,
            'title' => 'session objective 2',
            'session' => '4',
            'terms' => ['3'],
            'meshDescriptors' => [],
            'courseObjectives' => [],
            'descendants' => []
        ];

        $arr[] = [
            'id' => 3,
            'position' => 0,
            'active' => true,
            'title' => 'session objective 3',
            'session' => '4',
            'terms' => [],
            'meshDescriptors' => [],
            'courseObjectives' => [],
            'descendants' => []
        ];

        return $arr;
    }

    public function create()
    {
        return [
            'id' => 4,
            'position' => 0,
            'active' => true,
            'title' => 'session objective 4',
            'session' => '1',
            'terms' => [],
            'meshDescriptors' => [],
            'courseObjectives' => [],
            'descendants' => []
        ];
    }

    public function createInvalid()
    {
        return [];
    }

    public function getDtoClass(): string
    {
        return SessionObjectiveDTO::class;
    }
}
