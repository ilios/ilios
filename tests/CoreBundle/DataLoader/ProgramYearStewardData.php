<?php

namespace Tests\CoreBundle\DataLoader;

class ProgramYearStewardData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $arr[] = array(
            'id' => 1,
            'programYear' => '1',
            'school' => '1',
            'department' => '1'
        );
        $arr[] = array(
            'id' => 2,
            'programYear' => '1',
            'school' => '1',
            'department' => '2'
        );
        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 3,
            'programYear' => '2',
            'school' => '1',
            'department' => '1'
        );
    }

    public function createInvalid()
    {
        return [
            'programYear' => 11
        ];
    }
}
