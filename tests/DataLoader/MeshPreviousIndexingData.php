<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\MeshPreviousIndexingDTO;

final class MeshPreviousIndexingData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];
        $arr[] = [
            'id' => 1,
            'descriptor' => 'abc1',
            'previousIndexing' => 'something else',
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
            'previousIndexing' => 'something different',
        ];
    }

    public function createInvalid(): array
    {
        return [
            'id' => 'bad',
        ];
    }

    public function getDtoClass(): string
    {
        return MeshPreviousIndexingDTO::class;
    }
}
