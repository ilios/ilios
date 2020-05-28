<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

class PendingUserUpdateData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'type' => $this->faker->text(15),
            'property' => 'first property',
            'value' => $this->faker->text(25),
            'user' => '1',
        ];


        $arr[] = [
            'id' => 2,
            'type' => 'second type',
            'property' => $this->faker->text(5),
            'value' => 'second value',
            'user' => '4',
        ];

        return $arr;
    }

    public function create()
    {
        return [
            'id' => 3,
            'type' => $this->faker->text(15),
            'property' => $this->faker->text(5),
            'value' => $this->faker->text(25),
            'user' => '1',
        ];
    }

    public function createInvalid()
    {
        return [];
    }
}
