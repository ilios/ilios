<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\AamcMethodDTO;

class AamcMethodData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = [];

        $arr[] = [
            'id' => "AM001",
            'description' => $this->faker->text(),
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

    public function create()
    {
        return [
            'id' => 'FK',
            'description' => $this->faker->text(),
            'sessionTypes' => ['1'],
            'active' => true
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
        return AamcMethodDTO::class;
    }
}
