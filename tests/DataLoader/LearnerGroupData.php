<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\LearnerGroupDTO;

final class LearnerGroupData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'title' => 'first learner group',
            'location' => 'room 101',
            'cohort' => 1,
            'children' => ['4'],
            'ilmSessions' => ['1'],
            'offerings' => ['1'],
            'instructorGroups' => ['1'],
            'users' => ['2', '5'],
            'instructors' => ['1'],
            'descendants' => [],
            'url' => 'https://iliosproject.org',
            'needsAccommodation' => false,
            'parent' => null,
            'ancestor' => null,
        ];

        $arr[] = [
            'id' => 2,
            'title' => 'second learner group',
            'cohort' => 2,
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['2'],
            'instructorGroups' => [],
            'users' => ['2'],
            'instructors' => [],
            'descendants' => [],
            'needsAccommodation' => true,
            'parent' => null,
            'ancestor' => null,
        ];

        $arr[] = [
            'id' => 3,
            'title' => 'third learner group',
            'cohort' => 1,
            'children' => [],
            'ilmSessions' => ['1'],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => ['2'],
            'instructors' => ['1'],
            'descendants' => ['4'],
            'needsAccommodation' => false,
            'parent' => null,
            'ancestor' => null,
        ];


        $arr[] = [
            'id' => 4,
            'title' => 'fourth learner group',
            'location' => 'fourth location',
            'cohort' => 1,
            'children' => [],
            'parent' => 1,
            'ancestor' => 3,
            'ilmSessions' => [],
            'offerings' => [],
            'instructorGroups' => [],
            'users' => [],
            'instructors' => [],
            'descendants' => [],
            'url' => 'https://iliosproject.org',
            'needsAccommodation' => false,
        ];


        $arr[] = [
            'id' => 5,
            'title' => 'fifth learner group',
            'cohort' => 1,
            'children' => [],
            'ilmSessions' => [],
            'offerings' => ['1', '2'],
            'instructorGroups' => [],
            'users' => ['5'],
            'instructors' => [],
            'descendants' => [],
            'needsAccommodation' => false,
            'parent' => null,
            'ancestor' => null,
        ];


        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 6,
            'title' => 'sixth learner group',
            'cohort' => "1",
            'ancestor' => 2,
            'children' => [],
            'ilmSessions' => ['1'],
            'offerings' => ['1'],
            'instructorGroups' => [],
            'users' => [],
            'instructors' => [],
            'descendants' => [],
            'needsAccommodation' => false,
            'parent' => null,
        ];
    }

    public function createInvalid(): array
    {
        return [];
    }

    public function getDtoClass(): string
    {
        return LearnerGroupDTO::class;
    }
}
