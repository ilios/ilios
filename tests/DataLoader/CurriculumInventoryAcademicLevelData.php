<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\CurriculumInventoryAcademicLevelDTO;

class CurriculumInventoryAcademicLevelData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = [];
        $arr[] = [
            'id' => 1,
            'name' => $this->faker->text(10),
            'description' => 'first description',
            'level' => 1,
            'report' => 1,
            'sequenceBlocks' => ['1'],
        ];
        $arr[] = [
            'id' => 2,
            'name' => 'second name',
            'description' => $this->faker->text(100),
            'level' => 2,
            'report' => 1,
            'sequenceBlocks' => ['2', '3', '4', '5'],
        ];
        return $arr;
    }

    public function create()
    {
        $arr = [
            'id' => 3,
            'name' => $this->faker->text(10),
            'description' => $this->faker->text(100),
            'level' => 3,
            'report' => 1,
            'sequenceBlocks' => []
        ];
        return $arr;
    }

    public function createInvalid()
    {
        return [];
    }

    public function createMany($count)
    {
        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $arr = $this->create();
            $arr['id'] = $arr['id'] + $i;
            $arr['level'] = $arr['level'] + $i;
            $data[] = $arr;
        }

        return $data;
    }

    public function getDtoClass(): string
    {
        return CurriculumInventoryAcademicLevelDTO::class;
    }
}
