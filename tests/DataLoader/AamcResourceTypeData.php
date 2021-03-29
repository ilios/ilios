<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\AamcResourceTypeDTO;

/**
 * Class AamcResourceTypeData
 */
class AamcResourceTypeData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = [];

        $arr[] = [
            'id' => 'RE001',
            'title' => 'first title',
            'description' => $this->faker->text(),
            'terms' => ['1','2'],
        ];

        $arr[] = [
            'id' => 'RE002',
            'title' => $this->faker->text(100),
            'description' => 'second description',
            'terms' => ['2', '3'],
        ];

        $arr[] = [
            'id' => 'RE003',
            'title' => $this->faker->text(100),
            'description' => $this->faker->text(),
            'terms' => [],
        ];


        return $arr;
    }

    public function create()
    {
        return [
            'id' => 'FKRE',
            'title' => $this->faker->text(100),
            'description' => $this->faker->text(),
            'terms' => [],
        ];
    }

    public function createInvalid()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function createMany($count)
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
