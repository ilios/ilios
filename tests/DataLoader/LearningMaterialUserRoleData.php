<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

class LearningMaterialUserRoleData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'title' => $this->faker->text(10)
        ];

        $arr[] = [
            'id' => 2,
            'title' => 'second lm user role'
        ];

        return $arr;
    }

    public function create()
    {
        return [
            'id' => 3,
            'title' => $this->faker->text(10)
        ];
    }

    public function createInvalid()
    {
        return [];
    }
}
