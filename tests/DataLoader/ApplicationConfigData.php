<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\ApplicationConfigDTO;

class ApplicationConfigData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = [];
        $arr[] = [
            'id' => 1,
            'name' => '1' . $this->faker->text(50),
            'value' => $this->faker->text(100),
        ];
        $arr[] = [
            'id' => 2,
            'name' => 'second name',
            'value' => $this->faker->text(100),
        ];
        $arr[] = [
            'id' => 3,
            'name' => '2' . $this->faker->text(50),
            'value' => 'third value',
        ];

        return $arr;
    }

    public function create()
    {
        return [
            'id' => 4,
            'name' => '4' . $this->faker->text(50),
            'value' => $this->faker->text(100),
        ];
    }

    public function createInvalid()
    {
        return [
            'id' => 'dsfdsaf'
        ];
    }

    public function createJsonApi(array $arr): object
    {
        $item = $this->buildJsonApiObject($arr, ApplicationConfigDTO::class);
        return json_decode(json_encode(['data' => $item]), false);
    }
}
