<?php

namespace Tests\CoreBundle\DataLoader;

class AlertData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $arr[] = array(
            'id' => 1,
            'tableRowId' => 1,
            'tableName' => 'course',
            'dispatched' => '1',
            'changeTypes' => ['1'],
            'instigators' => ['1', '2'],
            'recipients' => ['1']
        );
        $arr[] = array(
            'id' => 2,
            'tableRowId' => 1,
            'tableName' => 'course',
            'dispatched' => '1',
            'changeTypes' => ['1'],
            'instigators' => [],
            'recipients' => []
        );
        $arr[] = array(
            'id' => 3,
            'tableRowId' => 1,
            'tableName' => 'course',
            'dispatched' => false,
            'changeTypes' => [],
            'instigators' => [],
            'recipients' => []
        );

        return $arr;
    }

    public function create()
    {
        return [
            'id' => 4,
            'tableRowId' => $this->faker->randomDigit,
            'tableName' => 'course',
            'dispatched' => '1',
            'changeTypes' => ['1'],
            'instigators' => [],
            'recipients' => []
        ];
    }

    public function createInvalid()
    {
        return [
            'id' => 'string',
            'changeTypes' => [232452],
            'instigators' => [3234],
            'recipients' => [32434]
        ];
    }
}
