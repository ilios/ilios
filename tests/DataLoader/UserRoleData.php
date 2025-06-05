<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\UserRoleDTO;

final class UserRoleData extends AbstractDataLoader
{
    protected function getData(): array
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

    public function create(): array
    {
        $arr = [];

        $arr[] = [
            'id' => 4,
            'title' => 'user role 4',
        ];

        return $arr[0];
    }

    public function createInvalid(): array
    {
        return [];
    }

    public function getDtoClass(): string
    {
        return UserRoleDTO::class;
    }
}
