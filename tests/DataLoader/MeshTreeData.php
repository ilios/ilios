<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\MeshTreeDTO;

class MeshTreeData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = [];
        $arr[] = [
            'id' => 1,
            'treeNumber' => $this->faker->text(31),
            'descriptor' => 'abc1'
        ];
        $arr[] = [
            'id' => 2,
            'treeNumber' => 'tree2',
            'descriptor' => 'abc1'
        ];

        return $arr;
    }

    public function create()
    {
        return [
            'id' => 3,
            'treeNumber' => $this->faker->word(),
            'descriptor' => 'abc2'
        ];
    }

    public function createInvalid()
    {
        return [
            'id' => 'bad'
        ];
    }

    public function getDtoClass(): string
    {
        return MeshTreeDTO::class;
    }
}
