<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\UserSessionMaterialStatusDTO;
use App\Entity\UserSessionMaterialStatusInterface;

final class UserSessionMaterialStatusData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];

        $arr[] = [
            'id' => 1,
            'status' => UserSessionMaterialStatusInterface::NONE,
            'user' => 2,
            'material' => 1,
        ];

        $arr[] = [
            'id' => 2,
            'status' => UserSessionMaterialStatusInterface::STARTED,
            'user' => 2,
            'material' => 3,
        ];

        $arr[] = [
            'id' => 3,
            'status' => UserSessionMaterialStatusInterface::COMPLETE,
            'user' => 2,
            'material' => 5,
        ];

        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 4,
            'status' => UserSessionMaterialStatusInterface::STARTED,
            'user' => 2,
            'material' => 1,
        ];
    }

    public function createInvalid(): array
    {
        return [];
    }

    public function getDtoClass(): string
    {
        return UserSessionMaterialStatusDTO::class;
    }
}
