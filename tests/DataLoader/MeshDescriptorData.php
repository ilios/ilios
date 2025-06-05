<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\MeshDescriptorDTO;

final class MeshDescriptorData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];
        $arr[] = [
            'id' => 'abc1',
            'name' => 'desc1',
            'annotation' => 'annotation1',
            'courses' => ["1"],
            'sessionLearningMaterials' => ['1'],
            'courseLearningMaterials' => ['1', '3'],
            'sessions' => ['1'],
            'concepts' => ['1', '2'],
            'qualifiers' => ['1', '2'],
            'trees' => ['1', '2'],
            'previousIndexing' => 1,
            'deleted' => false,
            'sessionObjectives' => ['2'],
            'courseObjectives' => ['1', '2'],
            'programYearObjectives' => ['1'],
        ];
        $arr[] = [
            'id' => 'abc2',
            'name' => 'desc2',
            'annotation' => 'annotation2',
            'courses' => [],
            'sessionLearningMaterials' => ["2", "3", "4", "5", "6", "7", "8"],
            'courseLearningMaterials' => [],
            'sessions' => ["3"],
            'concepts' => [],
            'qualifiers' => [],
            'trees' => [],
            'previousIndexing' => 2,
            'deleted' => false,
            'sessionObjectives' => ['1'],
            'courseObjectives' => ['4'],
            'programYearObjectives' => [],
        ];
        $arr[] = [
            'id' => 'abc3',
            'name' => 'desc3',
            'annotation' => 'annotation3',
            'courses' => [],
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [],
            'sessions' => [],
            'concepts' => [],
            'qualifiers' => [],
            'trees' => [],
            'previousIndexing' => null,
            'deleted' => false,
            'sessionObjectives' => ['3'],
            'courseObjectives' => ['3'],
            'programYearObjectives' => ['2'],
        ];

        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 'abc4',
            'name' => 'desc4',
            'annotation' => 'yes no up down',
            'courses' => ['1'],
            'sessionLearningMaterials' => ['1'],
            'courseLearningMaterials' => ['1'],
            'sessions' => ['1'],
            'concepts' => ['1'],
            'qualifiers' => ['1'],
            'trees' => [],
            'previousIndexing' => null,
            'deleted' => false,
            'sessionObjectives' => [],
            'courseObjectives' => [],
            'programYearObjectives' => [],
        ];
    }

    public function createInvalid(): array
    {
        return [
            'id' => 'bad',
        ];
    }

    /**
     * Mesh descriptor IDs are strings so we have to convert them
     * @inheritdoc
     */
    public function createMany(int $count): array
    {
        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $arr = $this->create();
            $arr['id'] = $arr['id'] . $i;
            $data[] = $arr;
        }

        return $data;
    }

    public function getDtoClass(): string
    {
        return MeshDescriptorDTO::class;
    }
}
