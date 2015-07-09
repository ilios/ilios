<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class CurriculumInventoryReportData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        
        $dt = $this->faker->dateTime;
        $dt->setTime(0, 0, 0);
        $arr[] = array(
            'id' => 1,
            'year' => '1999',
            'startDate' => $dt->format('c'),
            'endDate' => $dt->format('c')
        );

        $dt = $this->faker->dateTime;
        $dt->setTime(0, 0, 0);
        $arr[] = array(
            'id' => 10,
            'year' => '2001',
            'startDate' => $dt->format('c'),
            'endDate' => $dt->format('c')
        );

        return $arr;
    }

    public function create()
    {
        return [];
    }

    public function createInvalid()
    {
        return [];
    }
}
