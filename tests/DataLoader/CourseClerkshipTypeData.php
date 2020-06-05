<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\CourseClerkshipTypeDTO;

class CourseClerkshipTypeData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'title' => $this->faker->text(10),
            'courses' => ['1', '2']
        ];

        $arr[] = [
            'id' => 2,
            'title' => 'second clerkship type',
            'courses' => []
        ];


        return $arr;
    }

    public function create()
    {
        return [
            'id' => 3,
            'title' => $this->faker->text(10),
            'courses' => []
        ];
    }

    public function createInvalid()
    {
        return [];
    }

    public function getDtoClass(): string
    {
        return CourseClerkshipTypeDTO::class;
    }
}
