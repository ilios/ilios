<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\ApplicationConfigDTO;

final class ApplicationConfigData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];
        $arr[] = [
            'id' => 1,
            'name' => '1name',
            'value' => 'first value',
        ];
        $arr[] = [
            'id' => 2,
            'name' => 'second name',
            'value' => 'second value',
        ];
        $arr[] = [
            'id' => 3,
            'name' => '2name',
            'value' => 'third value',
        ];
        $arr[] = [
            'id' => 4,
            'name' => 'institution_domain',
            'value' => 'test.edu',
        ];

        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 5,
            'name' => '5name',
            'value' => 'fifth value',
        ];
    }

    public function createMany(int $count): array
    {
        $data = [];
        for ($i = 0; $i < $count; $i++) {
            $arr = $this->create();
            $arr['id'] = $arr['id'] + $i;
            $arr['name'] = 'name' . $i;
            $arr['value'] = 'value ' . $i;
            $data[] = $arr;
        }
        return $data;
    }

    public function createInvalid(): array
    {
        return [
            'id' => 'dsfdsaf',
        ];
    }

    public function getDtoClass(): string
    {
        return ApplicationConfigDTO::class;
    }
}
