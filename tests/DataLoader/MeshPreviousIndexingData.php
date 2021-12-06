<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\MeshPreviousIndexingDTO;

class MeshPreviousIndexingData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];
        $arr[] = [
            'id' => 1,
            'descriptor' => 'abc1',
            'previousIndexing' => $this->faker->text(),
        ];
        $arr[] = [
            'id' => 2,
            'descriptor' => 'abc2',
            'previousIndexing' => 'second previous indexing',
        ];

        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 3,
            'descriptor' => 'abc3',
            'previousIndexing' => $this->faker->text(),
        ];
    }

    public function createInvalid(): array
    {
        return [
            'id' => 'bad'
        ];
    }

    public function getDtoClass(): string
    {
        return MeshPreviousIndexingDTO::class;
    }
}
