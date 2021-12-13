<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\VocabularyDTO;

class VocabularyData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];
        $arr[] = [
            'id' => 1,
            'title' => $this->faker->text(100),
            'active' => true,
            'school' => 1,
            'terms' => ['1', '2', '3']
        ];
        $arr[] = [
            'id' => 2,
            'title' => 'second vocabulary',
            'active' => false,
            'school' => 2,
            'terms' => ['4', '5', '6']
        ];
        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 3,
            'title' => $this->faker->text(100),
            'active' => true,
            'school' => 2,
            'terms' => []
        ];
    }

    public function createInvalid(): array
    {
        return [
            'school' => 555,
        ];
    }

    public function getDtoClass(): string
    {
        return VocabularyDTO::class;
    }
}
