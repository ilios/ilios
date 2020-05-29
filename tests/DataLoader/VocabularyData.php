<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\VocabularyDTO;

class VocabularyData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = [];
        $arr[] = [
            'id' => 1,
            'title' => $this->faker->text(100),
            'active' => true,
            'school' => '1',
            'terms' => ['1', '2', '3']
        ];
        $arr[] = [
            'id' => 2,
            'title' => 'second vocabulary',
            'active' => false,
            'school' => '2',
            'terms' => ['4', '5', '6']
        ];
        return $arr;
    }

    public function create()
    {
        return [
            'id' => 3,
            'title' => $this->faker->text(100),
            'active' => true,
            'school' => '2',
            'terms' => []
        ];
    }

    public function createInvalid()
    {
        return [
            'school' => 555,
        ];
    }

    public function createJsonApi(array $arr): object
    {
        $item = $this->buildJsonApiObject($arr, VocabularyDTO::class);
        return json_decode(json_encode(['data' => $item]), false);
    }
}
