<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\LearningMaterialUserRoleDTO;

final class LearningMaterialUserRoleData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'title' => 'first lm user role',
        ];

        $arr[] = [
            'id' => 2,
            'title' => 'second lm user role',
        ];

        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 3,
            'title' => 'third lm user role',
        ];
    }

    public function createInvalid(): array
    {
        return [];
    }

    public function getDtoClass(): string
    {
        return LearningMaterialUserRoleDTO::class;
    }
}
