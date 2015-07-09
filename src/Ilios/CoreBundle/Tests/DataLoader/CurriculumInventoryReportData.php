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
            'year' => $this->faker->date('Y'),
            'name' => $this->faker->text(100),
            'description' => $this->faker->text(200),
            'startDate' => $dt->format('c'),
            'endDate' => $dt->format('c'),
        );

        $dt = $this->faker->dateTime;
        $dt->setTime(0, 0, 0);
        $arr[] = array(
            'id' => 2,
            'year' => $this->faker->date('Y'),
            'name' => $this->faker->text(100),
            'description' => $this->faker->text(200),
            'startDate' => $dt->format('c'),
            'endDate' => $dt->format('c'),
        );

        return $arr;
    }

    public function create()
    {
        $dt = $this->faker->dateTime;
        $dt->setTime(0, 0, 0);
        return array(
            'id' => 3,
            'year' => $this->faker->date('Y'),
            'name' => $this->faker->text(100),
            'description' => $this->faker->text(200),
            'startDate' => $dt->format('c'),
            'endDate' => $dt->format('c')
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
