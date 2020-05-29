<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\CurriculumInventorySequenceDTO;

class CurriculumInventorySequenceData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'report' => '1',
            'description' => $this->faker->text(100),
        ];

        $arr[] = [
            'id' => 2,
            'report' => '2',
            'description' => 'second description',
        ];

        return $arr;
    }

    public function create()
    {
        return [
            'id' => 3,
            'report' => '3',
            'description' => $this->faker->text(100),
        ];
    }

    public function createInvalid()
    {
        return [
            'report' => '4'
        ];
    }

    public function createJsonApi(array $arr): object
    {
        $item = $this->buildJsonApiObject($arr, CurriculumInventorySequenceDTO::class);
        return json_decode(json_encode(['data' => $item]), false);
    }
}
