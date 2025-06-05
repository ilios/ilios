<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\SessionObjectiveDTO;

final class SessionObjectiveData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'position' => 0,
            'active' => true,
            'title' => 'session objective 1',
            'session' => 1,
            'terms' => ['3', '4'],
            'meshDescriptors' => ['abc2'],
            'courseObjectives' => ['1'],
            'descendants' => ['3'],
            'ancestor' => null,
        ];

        $arr[] = [
            'id' => 2,
            'position' => 0,
            'active' => true,
            'title' => 'session objective 2',
            'session' => 4,
            'terms' => ['3'],
            'meshDescriptors' => ['abc1'],
            'courseObjectives' => ['2'],
            'descendants' => [],
            'ancestor' => null,
        ];

        $arr[] = [
            'id' => 3,
            'position' => 0,
            'active' => true,
            'title' => 'session objective 3',
            'session' => 4,
            'terms' => [],
            'meshDescriptors' => ['abc3'],
            'courseObjectives' => ['2'],
            'descendants' => [],
            'ancestor' => 1,
        ];

        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 4,
            'position' => 0,
            'active' => true,
            'title' => 'session objective 4',
            'session' => 1,
            'terms' => [],
            'meshDescriptors' => [],
            'courseObjectives' => [],
            'descendants' => [],
            'ancestor' => null,
        ];
    }

    public function createInvalid(): array
    {
        return [];
    }

    public function getDtoClass(): string
    {
        return SessionObjectiveDTO::class;
    }
}
