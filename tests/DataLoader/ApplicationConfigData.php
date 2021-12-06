<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\ApplicationConfigDTO;

class ApplicationConfigData extends AbstractDataLoader
{
    protected function getData(): array
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
        $arr[] = [
            'id' => 4,
            'name' => 'institution_domain',
            'value' => 'test.edu'
        ];

        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 5,
            'name' => '5' . $this->faker->text(50),
            'value' => $this->faker->text(100),
        ];
    }

    public function createInvalid(): array
    {
        return [
            'id' => 'dsfdsaf'
        ];
    }

    public function getDtoClass(): string
    {
        return ApplicationConfigDTO::class;
    }
}
