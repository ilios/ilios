<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\LearningMaterialStatusDTO;
use App\Entity\LearningMaterialStatusInterface;

class LearningMaterialStatusData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = [];

        $arr[] = [
            'id' => LearningMaterialStatusInterface::IN_DRAFT,
            'title' => 'Draft'
        ];
        $arr[] = [
            'id' => LearningMaterialStatusInterface::FINALIZED,
            'title' => 'Final'
        ];
        $arr[] = [
            'id' => LearningMaterialStatusInterface::REVISED,
            'title' => 'Revised'
        ];

        return $arr;
    }

    public function create()
    {
        return [
            'id' => 4,
            'title' => $this->faker->text(10)
        ];
    }

    public function createInvalid()
    {
        return [];
    }

    public function getDtoClass(): string
    {
        return LearningMaterialStatusDTO::class;
    }
}
