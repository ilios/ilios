<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\MeshTreeDTO;

final class MeshTreeData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];
        $arr[] = [
            'id' => 1,
            'treeNumber' => 'tree1',
            'descriptor' => 'abc1',
        ];
        $arr[] = [
            'id' => 2,
            'treeNumber' => 'tree2',
            'descriptor' => 'abc1',
        ];

        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 3,
            'treeNumber' => 'tree3',
            'descriptor' => 'abc2',
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
        return MeshTreeDTO::class;
    }
}
