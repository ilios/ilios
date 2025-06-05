<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\CourseObjectiveDTO;

final class CourseObjectiveData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'position' => 0,
            'active' => true,
            'title' => 'course objective 1',
            'course' => 1,
            'terms' => ['1', '4'],
            'meshDescriptors' => ['abc1'],
            'sessionObjectives' => ['1'],
            'programYearObjectives' => ['1'],
            'descendants' => ['2'],
            'ancestor' => null,
        ];

        $arr[] = [
            'id' => 2,
            'position' => 0,
            'active' => true,
            'title' => 'course objective 2',
            'course' => 2,
            'terms' => ['1'],
            'meshDescriptors' => ['abc1'],
            'sessionObjectives' => ['2', '3'],
            'programYearObjectives' => [],
            'descendants' => [],
            'ancestor' => 1,
        ];

        $arr[] = [
            'id' => 3,
            'position' => 0,
            'active' => true,
            'title' => 'course objective 3',
            'course' => 4,
            'terms' => [],
            'meshDescriptors' => ['abc3'],
            'sessionObjectives' => [],
            'programYearObjectives' => [],
            'descendants' => [],
            'ancestor' => null,
        ];

        $arr[] = [
            'id' => 4,
            'position' => 0,
            'active' => true,
            'title' => 'course objective 4',
            'course' => 2,
            'terms' => [],
            'meshDescriptors' => ['abc2'],
            'sessionObjectives' => [],
            'programYearObjectives' => [],
            'descendants' => [],
            'ancestor' => null,
        ];

        $arr[] = [
            'id' => 5,
            'position' => 0,
            'active' => true,
            'title' => 'course objective 5',
            'course' => 3,
            'terms' => [],
            'meshDescriptors' => [],
            'sessionObjectives' => [],
            'programYearObjectives' => [],
            'descendants' => [],
            'ancestor' => null,
        ];

        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 6,
            'position' => 0,
            'active' => true,
            'title' => 'course objective 6',
            'course' => 1,
            'terms' => [],
            'meshDescriptors' => [],
            'sessionObjectives' => [],
            'programYearObjectives' => [],
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
        return CourseObjectiveDTO::class;
    }
}
