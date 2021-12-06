<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\MeshTermDTO;

class MeshTermData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];
        $arr[] = [
            'id' => 1,
            'meshTermUid' => 'tuid' . $this->faker->text(5),
            'name' => 'term' . $this->faker->text(),
            'lexicalTag' => 'first tag',
            'conceptPreferred' => true,
            'recordPreferred' => false,
            'permuted' => true,
            'concepts' => ['1']
        ];
        $arr[] = [
            'id' => 2,
            'meshTermUid' => 'uid2',
            'name' => 'second term',
            'lexicalTag' => 'tag' . $this->faker->text(5),
            'conceptPreferred' => false,
            'recordPreferred' => true,
            'permuted' => false,
            'concepts' => ['1']
        ];

        return $arr;
    }

    public function create(): array
    {

        return [
            'id' => 3,
            'meshTermUid' => 'tuid123',
            'name' => $this->faker->text(192),
            'lexicalTag' => $this->faker->text(12),
            'conceptPreferred' => true,
            'recordPreferred' => true,
            'permuted' => true,
            'concepts' => ['1']
        ];
    }

    public function createInvalid(): array
    {
        return [
            'id' => 'bad'
        ];
    }

    public function getDtoClass(): string
    {
        return MeshTermDTO::class;
    }
}
