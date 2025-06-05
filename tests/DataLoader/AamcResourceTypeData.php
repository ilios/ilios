<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\AamcResourceTypeDTO;

/**
 * Class AamcResourceTypeData
 */
final class AamcResourceTypeData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];

        $arr[] = [
            'id' => 'RE001',
            'title' => 'first title',
            'description' => 'first description',
            'terms' => ['1','2'],
        ];

        $arr[] = [
            'id' => 'RE002',
            'title' => 'second title',
            'description' => 'second description',
            'terms' => ['2', '3'],
        ];

        $arr[] = [
            'id' => 'RE003',
            'title' => 'third title',
            'description' => 'third description',
            'terms' => [],
        ];


        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 'FKRE',
            'title' => 'new title',
            'description' => 'new description',
            'terms' => [],
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
        return AamcResourceTypeDTO::class;
    }
}
