<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\CurriculumInventoryExportDTO;
use Exception;

final class CurriculumInventoryExportData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'report' => 2,
            'document' => 'lorem ipsum',
            'createdBy' => 1,
        ];

        $arr[] = [
            'id' => 2,
            'report' => 3,
            'document' => 'dev/null',
            'createdBy' => 1,
        ];
        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 3,
            'report' => 1,
        ];
    }

    public function createInvalid(): array
    {
        return [];
    }

    public function getDtoClass(): string
    {
        return CurriculumInventoryExportDTO::class;
    }
}
