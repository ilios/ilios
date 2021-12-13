<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\SchoolConfigDTO;

class SchoolConfigData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];
        $arr[] = [
            'id' => 1,
            'name' => '1' . $this->faker->text(50),
            'value' => $this->faker->text(100),
            'school' => 1,
        ];
        $arr[] = [
            'id' => 2,
            'name' => 'second config',
            'value' => $this->faker->text(100),
            'school' => 1,
        ];
        $arr[] = [
            'id' => 3,
            'name' => '3' . $this->faker->text(50),
            'value' => 'third value',
            'school' => 2,
        ];
        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 4,
            'name' => '4' . $this->faker->text(50),
            'value' => $this->faker->text(100),
            'school' => 1,
        ];
    }

    public function createInvalid(): array
    {
        return [
            'name' => null,
        ];
    }

    public function getDtoClass(): string
    {
        return SchoolConfigDTO::class;
    }
}
