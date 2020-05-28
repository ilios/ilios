<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

class ProgramYearStewardData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = [];
        $arr[] = [
            'id' => 1,
            'programYear' => '1',
            'school' => '1',
            'department' => '1'
        ];
        $arr[] = [
            'id' => 2,
            'programYear' => '1',
            'school' => '1',
            'department' => '2'
        ];
        return $arr;
    }

    public function create()
    {
        return [
            'id' => 3,
            'programYear' => '2',
            'school' => '1',
            'department' => '1'
        ];
    }

    public function createInvalid()
    {
        return [
            'programYear' => 11
        ];
    }
}
