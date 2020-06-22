<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\CourseObjectiveDTO;

class CourseObjectiveData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'position' => 0,
            'active' => true,
            'title' => 'course objective 1',
            'course' => '1',
            'terms' => ['1', '4'],
            'meshDescriptors' => [],
            'sessionObjectives' => [],
            'programYearObjectives' => [],
            'ancestor' => null,
            'descendants' => []
        ];

        $arr[] = [
            'id' => 2,
            'position' => 0,
            'active' => true,
            'title' => 'course objective 2',
            'course' => '2',
            'terms' => ['1'],
            'meshDescriptors' => [],
            'sessionObjectives' => [],
            'programYearObjectives' => [],
            'ancestor' => null,
            'descendants' => []
        ];

        $arr[] = [
            'id' => 3,
            'position' => 0,
            'active' => true,
            'title' => 'course objective 3',
            'course' => '4',
            'terms' => [],
            'meshDescriptors' => [],
            'sessionObjectives' => [],
            'programYearObjectives' => [],
            'ancestor' => null,
            'descendants' => []
        ];

        $arr[] = [
            'id' => 4,
            'position' => 0,
            'active' => true,
            'title' => 'course objective 4',
            'course' => '2',
            'terms' => [],
            'meshDescriptors' => [],
            'sessionObjectives' => [],
            'programYearObjectives' => [],
            'ancestor' => null,
            'descendants' => []
        ];

        $arr[] = [
            'id' => 5,
            'position' => 0,
            'active' => true,
            'title' => 'course objective 5',
            'course' => '3',
            'terms' => [],
            'meshDescriptors' => [],
            'sessionObjectives' => [],
            'programYearObjectives' => [],
            'ancestor' => null,
            'descendants' => []
        ];

        return $arr;
    }

    public function create()
    {
        return [
            'id' => 6,
            'position' => 0,
            'active' => true,
            'title' => 'course objective 6',
            'course' => '1',
            'terms' => [],
            'meshDescriptors' => [],
            'sessionObjectives' => [],
            'programYearObjectives' => [],
            'ancestor' => null,
            'descendants' => []
        ];
    }

    public function createInvalid()
    {
        return [];
    }

    public function getDtoClass(): string
    {
        return CourseObjectiveDTO::class;
    }
}
