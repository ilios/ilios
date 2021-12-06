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
            'name' => 'concept' . $this->faker->text(),
            'preferred' => true,
            'scopeNote' => 'first scopeNote',
            'casn1Name' => 'casn' . $this->faker->text(120),
            'registryNumber' => $this->faker->text(20),
            'terms' => ['1', '2'],
            'descriptors' => ['abc1']
        ];
        $arr[] = [
            'id' => '2',
            'name' => 'second concept',
            'preferred' => false,
            'scopeNote' => 'scopeNote' . $this->faker->text(),
            'casn1Name' => 'second casn',
            'registryNumber' => 'abcd',
            'terms' => [],
            'descriptors' => ['abc1']
        ];

        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => '3',
            'name' => 'concept' . $this->faker->text(180),
            'preferred' => true,
            'scopeNote' => 'scopeNote' . $this->faker->text(),
            'casn1Name' => 'casn' . $this->faker->text(120),
            'registryNumber' => $this->faker->text(20),
            'terms' => ['1'],
            'descriptors' => []
        ];
    }

    public function createInvalid(): array
    {
        return [
            'id' => 'bad'
        ];
    }

    /**
     * Mesh concept IDs are strings so we have to convert them
     * @inheritdoc
     */
    public function createMany($count): array
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
