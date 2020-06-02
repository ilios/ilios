<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\DepartmentDTO;

class DepartmentData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'title' => $this->faker->text(50),
            'school' => '1',
            'stewards' => ['1']
        ];

        $arr[] = [
            'id' => 2,
            'title' => 'second department',
            'school' => '1',
            'stewards' => ['2'],
        ];

        $arr[] = [
            'id' => 3,
            'title' => $this->faker->text(50),
            'school' => '2',
            'stewards' => [],
        ];


        return $arr;
    }

    public function create()
    {
        return [
            'id' => 4,
            'title' => $this->faker->text(50),
            'school' => '1',
            'stewards' => []
        ];
    }

    public function createInvalid()
    {
        return [];
    }

    public function getDtoClass(): string
    {
        return DepartmentDTO::class;
    }
}
