<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\AssessmentOptionDTO;

class AssessmentOptionData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'name' => $this->faker->word(),
            'sessionTypes' => [1]
        ];

        $arr[] = [
            'id' => 2,
            'name' => 'second option',
            'sessionTypes' => [2]
        ];
        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 3,
            'name' => $this->faker->text(10),
            'sessionTypes' => []
        ];
    }

    public function createInvalid(): array
    {
        return [
            'id' => 'something',
            'name' => $this->faker->text()
        ];
    }

    public function createMany($count): array
    {
        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $arr = $this->create();
            $arr['id'] = $arr['id'] + $i;
            $arr['name'] = $arr['id'] . $this->faker->word();
            $data[] = $arr;
        }

        return $data;
    }

    public function getDtoClass(): string
    {
        return AssessmentOptionDTO::class;
    }
}
