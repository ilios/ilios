<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\CourseDTO;

final class CourseData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'title' => 'firstCourse',
            'level' => 1,
            'year' => 2016,
            'startDate' => "2016-09-04T00:00:00+00:00",
            'endDate' => "2017-01-01T00:00:00+00:00",
            'externalId' => 'first',
            'locked' => false,
            'archived' => false,
            'publishedAsTbd' => false,
            'published' => true,
            'school' => "1",
            'clerkshipType' => "1",
            'directors' => ['1'],
            'administrators' => ['1'],
            'studentAdvisors' => ['2'],
            'cohorts' => ['1'],
            'terms' => ['1'],
            'courseObjectives' => ['1'],
            'meshDescriptors' => ["abc1"],
            'learningMaterials' => ['1', '2', '4', '5', '6', '7', '8', '9', '10'],
            'sessions' => ['1', '2'],
            'descendants' => [],
            'ancestor' => null,
        ];

        $arr[] = [
            'id' => 2,
            'title' => 'course 2',
            'level' => 1,
            'year' => 2012,
            'startDate' => "2013-09-01T00:00:00+00:00",
            'endDate' => "2013-12-14T00:00:00+00:00",
            'externalId' => 'second',
            'locked' => false,
            'archived' => false,
            'publishedAsTbd' => false,
            'published' => false,
            'school' => "1",
            'clerkshipType' => "1",
            'directors' => ['2'],
            'administrators' => [],
            'studentAdvisors' => [],
            'cohorts' => ['1'],
            'terms' => ['1', '4'],
            'courseObjectives' => ['2', '4'],
            'meshDescriptors' => [],
            'learningMaterials' => [],
            'sessions' => ['3', '5', '6', '7', '8'],
            'descendants' => [],
            'ancestor' => null,
        ];

        $arr[] = [
            'id' => 3,
            'title' => 'third',
            'level' => 1,
            'year' => 2012,
            'startDate' => "2013-09-01T00:00:00+00:00",
            'endDate' => "2013-12-14T00:00:00+00:00",
            'externalId' => 'course3',
            'locked' => false,
            'archived' => false,
            'publishedAsTbd' => false,
            'published' => false,
            'school' => "2",
            'clerkshipType' => null,
            'directors' => ["4"],
            'administrators' => [],
            'studentAdvisors' => ['3'],
            'cohorts' => ["2"],
            'terms' => [],
            'courseObjectives' => ['5'],
            'meshDescriptors' => [],
            'learningMaterials' => [],
            'sessions' => [],
            'descendants' => ['4'],
            'ancestor' => null,
        ];

        $arr[] = [
            'id' => 4,
            'title' => 'fourth course',
            'level' => 3,
            'year' => 2013,
            'startDate' => "2013-09-01T00:00:00+00:00",
            'endDate' => "2013-12-14T00:00:00+00:00",
            'externalId' => 'fourth',
            'locked' => false,
            'archived' => false,
            'publishedAsTbd' => false,
            'published' => false,
            'school' => "2",
            'clerkshipType' => null,
            'directors' => ["2"],
            'administrators' => [],
            'studentAdvisors' => [],
            'cohorts' => ["3"],
            'terms' => ['3', '6'],
            'courseObjectives' => ['3'],
            'meshDescriptors' => [],
            'learningMaterials' => ["3"],
            'sessions' => ["4"],
            'ancestor' => 3,
            'descendants' => [],
        ];

        $arr[] = [
            'id' => 5,
            'title' => 'fifth Course',
            'level' => 3,
            'year' => 2013,
            'startDate' => "2017-02-14T00:00:00+00:00",
            'endDate' => "2017-02-17T00:00:00+00:00",
            'externalId' => 'fifth',
            'locked' => true,
            'archived' => true,
            'publishedAsTbd' => true,
            'published' => true,
            'school' => "2",
            'clerkshipType' => null,
            'directors' => [],
            'administrators' => ['4'],
            'studentAdvisors' => [],
            'cohorts' => ["3"],
            'terms' => [],
            'courseObjectives' => [],
            'meshDescriptors' => [],
            'learningMaterials' => [],
            'sessions' => [],
            'descendants' => [],
            'ancestor' => null,
        ];

        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 6,
            'title' => 'sixth Course',
            'level' => 1,
            'year' => 2013,
            'startDate' => "2013-09-01T00:00:00+00:00",
            'endDate' => "2013-12-14T00:00:00+00:00",
            'externalId' => '12344321',
            'locked' => false,
            'archived' => false,
            'publishedAsTbd' => false,
            'published' => false,
            'school' => "1",
            'clerkshipType' => "1",
            'directors' => [],
            'administrators' => [],
            'studentAdvisors' => [],
            'cohorts' => [],
            'terms' => [],
            'courseObjectives' => [],
            'meshDescriptors' => [],
            'learningMaterials' => [],
            'sessions' => [],
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
        return CourseDTO::class;
    }
}
