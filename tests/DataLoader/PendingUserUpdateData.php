<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\PendingUserUpdateDTO;

final class PendingUserUpdateData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'type' => 'first type',
            'property' => 'first property',
            'value' => 'first value',
            'user' => 1,
        ];


        $arr[] = [
            'id' => 2,
            'type' => 'second type',
            'property' => 'second property',
            'value' => 'second value',
            'user' => 4,
        ];

        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 3,
            'type' => 'third type',
            'property' => 'third property',
            'value' => 'third value',
            'user' => 1,
        ];
    }

    public function createInvalid(): array
    {
        return [];
    }

    public function getDtoClass(): string
    {
        return PendingUserUpdateDTO::class;
    }
}
