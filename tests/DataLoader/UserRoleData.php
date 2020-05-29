<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\UserRoleDTO;

class UserRoleData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'title' => 'Developer',
        ];

        $arr[] = [
            'id' => 2,
            'title' => 'Something Else',
        ];

        $arr[] = [
            'id' => 3,
            'title' => 'Course Director',
        ];


        return $arr;
    }

    public function create()
    {
        $arr = [];

        $arr[] = [
            'id' => 4,
            'title' => $this->faker->text(10)
        ];

        return $arr[0];
    }

    public function createInvalid()
    {
        return [];
    }

    public function createJsonApi(array $arr): object
    {
        $item = $this->buildJsonApiObject($arr, UserRoleDTO::class);
        return json_decode(json_encode(['data' => $item]), false);
    }
}
