<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\ProgramYearObjectiveDTO;

final class ProgramYearObjectiveData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'position' => 0,
            'active' => true,
            'title' => 'program year objective 1',
            'programYear' => 1,
            'terms' => ['2', '4'],
            'meshDescriptors' => ['abc1'],
            'courseObjectives' => ['1'],
            'descendants' => ['2'],
            'ancestor' => null,
            'competency' => 1,
        ];

        $arr[] = [
            'id' => 2,
            'position' => 0,
            'active' => true,
            'title' => 'program year objective 2',
            'programYear' => 5,
            'terms' => ['2'],
            'meshDescriptors' => ['abc3'],
            'courseObjectives' => [],
            'descendants' => [],
            'ancestor' => 1,
            'competency' => '2',
        ];

        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 3,
            'position' => 0,
            'active' => true,
            'title' => 'program year objective 3',
            'programYear' => 2,
            'terms' => [],
            'meshDescriptors' => [],
            'courseObjectives' => [],
            'ancestor' => null,
            'descendants' => [],
        ];
    }

    public function createInvalid(): array
    {
        return [];
    }

    public function getDtoClass(): string
    {
        return ProgramYearObjectiveDTO::class;
    }
}
