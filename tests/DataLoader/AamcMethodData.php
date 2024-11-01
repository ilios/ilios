<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\AamcMethodDTO;

class AamcMethodData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];

        $arr[] = [
            'id' => "AM001",
            'description' => 'some words',
            'sessionTypes' => ['1', '2'],
            'active' => true,
        ];

        $arr[] = [
            'id' => "AM002",
            'description' => 'filterable description',
            'sessionTypes' => [],
            'active' => false,
        ];

        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 'FK',
            'description' => 'method description',
            'sessionTypes' => ['1'],
            'active' => true,
        ];
    }

    public function createInvalid(): array
    {
        return [];
    }

    public function createMany(int $count): array
    {
        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $arr = $this->create();
            $arr['id'] = $arr['id'] . $i;
            $data[] = $arr;
        }

        return $data;
    }

    public function getDtoClass(): string
    {
        return AamcMethodDTO::class;
    }
}
