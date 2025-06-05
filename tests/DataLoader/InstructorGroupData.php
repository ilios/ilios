<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\InstructorGroupDTO;

final class InstructorGroupData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'title' => 'first instructor group',
            'school' => 1,
            'learnerGroups' => ['1'],
            'ilmSessions' => ['1'],
            'users' => ['2'],
            'offerings' => ['1'],
        ];

        $arr[] = [
            'id' => 2,
            'title' => 'second instructor group',
            'school' => 1,
            'learnerGroups' => [],
            'ilmSessions' => [],
            'users' => ['2', '4'],
            'offerings' => ['3'],
        ];

        $arr[] = [
            'id' => 3,
            'title' => 'third instructor group',
            'school' => 1,
            'learnerGroups' => [],
            'ilmSessions' => ['2'],
            'users' => ['2'],
            'offerings' => [],
        ];

        $arr[] = [
            'id' => 4,
            'title' => 'fourth instructor group',
            'school' => 2,
            'learnerGroups' => [],
            'ilmSessions' => [],
            'users' => [],
            'offerings' => [],
        ];


        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 5,
            'title' => 'fifth instructor group',
            'school' => 1,
            'learnerGroups' => ['1'],
            'ilmSessions' => ['1'],
            'users' => [],
            'offerings' => [],
        ];
    }

    public function createInvalid(): array
    {
        return [];
    }

    public function getDtoClass(): string
    {
        return InstructorGroupDTO::class;
    }
}
