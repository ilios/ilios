<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\CourseClerkshipTypeDTO;

final class CourseClerkshipTypeData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'title' => 'first clerkship type',
            'courses' => ['1', '2'],
        ];

        $arr[] = [
            'id' => 2,
            'title' => 'second clerkship type',
            'courses' => [],
        ];


        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 3,
            'title' => 'third clerkship type',
            'courses' => [],
        ];
    }

    public function createInvalid(): array
    {
        return [];
    }

    public function getDtoClass(): string
    {
        return CourseClerkshipTypeDTO::class;
    }
}
