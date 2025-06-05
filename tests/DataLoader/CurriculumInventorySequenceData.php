<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\CurriculumInventorySequenceDTO;

final class CurriculumInventorySequenceData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'report' => 1,
            'description' => 'first description',
        ];

        $arr[] = [
            'id' => 2,
            'report' => 2,
            'description' => 'second description',
        ];

        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 3,
            'report' => 3,
            'description' => 'third description',
        ];
    }

    public function createInvalid(): array
    {
        return [
            'report' => '4',
        ];
    }

    public function getDtoClass(): string
    {
        return CurriculumInventorySequenceDTO::class;
    }
}
