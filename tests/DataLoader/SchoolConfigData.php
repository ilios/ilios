<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\DTO\SchoolConfigDTO;

final class SchoolConfigData extends AbstractDataLoader
{
    protected function getData(): array
    {
        $arr = [];
        $arr[] = [
            'id' => 1,
            'name' => '1bar',
            'value' => 'lorem ipsum',
            'school' => 1,
        ];
        $arr[] = [
            'id' => 2,
            'name' => 'second config',
            'value' => 'dev/null',
            'school' => 1,
        ];
        $arr[] = [
            'id' => 3,
            'name' => '3foo',
            'value' => 'third value',
            'school' => 2,
        ];
        return $arr;
    }

    public function create(): array
    {
        return [
            'id' => 4,
            'name' => '4baz',
            'value' => 'fourth value',
            'school' => 1,
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
            'name' => str_repeat('toolong', 20),
        ];
    }

    public function getDtoClass(): string
    {
        return SchoolConfigDTO::class;
    }
}
