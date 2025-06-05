<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\CurriculumInventoryAcademicLevelDTO;

final class CurriculumInventoryAcademicLevelData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];
        $arr[] = [
            'id' => 1,
            'name' => 'first name',
            'description' => 'first description',
            'level' => 1,
            'report' => 1,
            'startingSequenceBlocks' => ['1'],
            'endingSequenceBlocks' => [],
        ];
        $arr[] = [
            'id' => 2,
            'name' => 'second name',
            'description' => 'second description',
            'level' => 2,
            'report' => 1,
            'startingSequenceBlocks' => ['2', '3', '4', '5'],
            'endingSequenceBlocks' => ['1'],
        ];
        $arr[] = [
            'id' => 3,
            'name' => 'third name',
            'description' => 'third description',
            'level' => 3,
            'report' => 1,
            'startingSequenceBlocks' => [],
            'endingSequenceBlocks' => ['2', '3', '4', '5'],
        ];
        return $arr;
    }

    public function create(): array
    {
        $arr = [
            'id' => 4,
            'name' => 'fourth name',
            'description' => 'fourth description',
            'level' => 4,
            'report' => 1,
            'startingSequenceBlocks' => [],
            'endingSequenceBlocks' => [],
        ];
        return $arr;
    }

    public function createInvalid(): array
    {
        return [];
    }

    public function createMany(int $count): array
    {
        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $arr = $this->create();
            $arr['id'] = $arr['id'] + $i;
            $arr['level'] = $arr['level'] + $i;
            $data[] = $arr;
        }

        return $data;
    }

    public function getDtoClass(): string
    {
        return CurriculumInventoryAcademicLevelDTO::class;
    }
}
