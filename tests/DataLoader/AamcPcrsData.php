<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\AamcPcrsDTO;

class AamcPcrsData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];
        $arr[] = [
            'id' => 'aamc-pcrs-comp-c0101',
            'description' => $this->faker->text(),
            'competencies' => [1,2]
        ];
        $arr[] = [
            'id' => 'aamc-pcrs-comp-c0102',
            'description' => 'second description',
            'competencies' => [2,3]
        ];

        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 'fk-',
            'description' => $this->faker->text(),
            'competencies' => [1]
        ];
    }

    public function createInvalid(): array
    {
        return [
            'id' => $this->faker->text(),
            'competencies' => [454098430958]
        ];
    }

    public function createMany($count): array
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
