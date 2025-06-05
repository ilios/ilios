<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\AlertChangeTypeInterface;
use App\Entity\DTO\AlertChangeTypeDTO;

final class AlertChangeTypeData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];

        $arr[] = [
            'id' => AlertChangeTypeInterface::CHANGE_TYPE_TIME,
            'title' => 'first title',
            'alerts' => ['1', '2'],
        ];

        $arr[] = [
            'id' => AlertChangeTypeInterface::CHANGE_TYPE_LOCATION,
            'title' => 'second title',
            'alerts' => [],
        ];

        $arr[] = [
            'id' => AlertChangeTypeInterface::CHANGE_TYPE_LEARNING_MATERIAL,
            'title' => 'third title',
            'alerts' => [],
        ];

        $arr[] = [
            'id' => AlertChangeTypeInterface::CHANGE_TYPE_INSTRUCTOR,
            'title' => 'fourth title',
            'alerts' => [],
        ];

        $arr[] = [
            'id' => AlertChangeTypeInterface::CHANGE_TYPE_COURSE_DIRECTOR,
            'title' => 'fifth title',
            'alerts' => [],
        ];

        $arr[] = [
            'id' => AlertChangeTypeInterface::CHANGE_TYPE_LEARNER_GROUP,
            'title' => 'sixth title',
            'alerts' => [],
        ];

        $arr[] = [
            'id' => AlertChangeTypeInterface::CHANGE_TYPE_NEW_OFFERING,
            'title' => 'seventh title',
            'alerts' => [],
        ];

        $arr[] = [
            'id' => AlertChangeTypeInterface::CHANGE_TYPE_SESSION_PUBLISH,
            'title' => 'eighth title',
            'alerts' => [],
        ];

        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 9,
            'title' => 'ninth title',
            'alerts' => ['1'],
        ];
    }

    public function createInvalid(): array
    {
        return [
            'id' => 'something',
            'alerts' => [424524],
        ];
    }

    public function getDtoClass(): string
    {
        return AlertChangeTypeDTO::class;
    }
}
