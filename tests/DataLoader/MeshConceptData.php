<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\MeshConceptDTO;

class MeshConceptData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];
        $arr[] = [
            'id' => '1',
            'name' => 'concept one',
            'preferred' => true,
            'scopeNote' => 'first scopeNote',
            'casn1Name' => 'casn123456',
            'registryNumber' => 'abc1234',
            'terms' => ['1', '2'],
            'descriptors' => ['abc1'],
        ];
        $arr[] = [
            'id' => '2',
            'name' => 'second concept',
            'preferred' => false,
            'scopeNote' => 'scopeNote two',
            'casn1Name' => 'second casn',
            'registryNumber' => 'abcd',
            'terms' => [],
            'descriptors' => ['abc1'],
        ];

        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => '3',
            'name' => 'concept three',
            'preferred' => true,
            'scopeNote' => 'scopeNote123',
            'casn1Name' => 'casn1122433',
            'registryNumber' => '112233',
            'terms' => ['1'],
            'descriptors' => [],
        ];
    }

    public function createInvalid(): array
    {
        return [
            'id' => 'bad',
        ];
    }

    /**
     * Mesh concept IDs are strings so we have to convert them
     * @inheritdoc
     */
    public function createMany(int $count): array
    {
        $data = parent::createMany($count);

        return array_map(function (array $arr) {
            $arr['id'] = (string) $arr['id'];

            return $arr;
        }, $data);
    }

    public function getDtoClass(): string
    {
        return MeshConceptDTO::class;
    }
}
