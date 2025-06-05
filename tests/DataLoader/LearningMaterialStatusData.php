<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\LearningMaterialStatusDTO;
use App\Entity\LearningMaterialStatusInterface;

final class LearningMaterialStatusData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];

        $arr[] = [
            'id' => LearningMaterialStatusInterface::IN_DRAFT,
            'title' => 'Draft',
        ];
        $arr[] = [
            'id' => LearningMaterialStatusInterface::FINALIZED,
            'title' => 'Final',
        ];
        $arr[] = [
            'id' => LearningMaterialStatusInterface::REVISED,
            'title' => 'Revised',
        ];

        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 4,
            'title' => 'new status',
        ];
    }

    public function createInvalid(): array
    {
        return [];
    }

    public function getDtoClass(): string
    {
        return LearningMaterialStatusDTO::class;
    }
}
