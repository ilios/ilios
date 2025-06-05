<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\AamcPcrsDTO;

final class AamcPcrsData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];
        $arr[] = [
            'id' => 'aamc-pcrs-comp-c0101',
            'description' => 'first description',
            'competencies' => [1,2],
        ];
        $arr[] = [
            'id' => 'aamc-pcrs-comp-c0102',
            'description' => 'second description',
            'competencies' => [2,3],
        ];

        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 'fk-',
            'description' => 'some other description',
            'competencies' => [1],
        ];
    }

    public function createInvalid(): array
    {
        return [
            'id' => 'key',
            'competencies' => [454098430958],
        ];
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
        return AamcPcrsDTO::class;
    }
}
