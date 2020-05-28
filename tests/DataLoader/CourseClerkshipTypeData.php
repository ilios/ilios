<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

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
}
