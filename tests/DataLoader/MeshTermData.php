<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\MeshTermDTO;

final class MeshTermData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];
        $arr[] = [
            'id' => 1,
            'meshTermUid' => 'tuid one' ,
            'name' => 'term one',
            'lexicalTag' => 'first tag',
            'conceptPreferred' => true,
            'recordPreferred' => false,
            'permuted' => true,
            'concepts' => ['1'],
        ];
        $arr[] = [
            'id' => 2,
            'meshTermUid' => 'uid2',
            'name' => 'second term',
            'lexicalTag' => 'tag two',
            'conceptPreferred' => false,
            'recordPreferred' => true,
            'permuted' => false,
            'concepts' => ['1'],
        ];

        return $arr;
    }

    public function create(): array
    {

        return [
            'id' => 3,
            'meshTermUid' => 'tuid123',
            'name' => 'third term',
            'lexicalTag' => 'some tag',
            'conceptPreferred' => true,
            'recordPreferred' => true,
            'permuted' => true,
            'concepts' => ['1'],
        ];
    }

    public function createInvalid(): array
    {
        return [
            'id' => 'bad',
        ];
    }

    public function getDtoClass(): string
    {
        return MeshTermDTO::class;
    }
}
