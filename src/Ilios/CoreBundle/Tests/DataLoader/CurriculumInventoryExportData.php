<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class CurriculumInventoryExportData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $dt = $this->faker->dateTime;
        $dt->setTime(0, 0, 0);

        $arr[] = array(
            'report_id' => 1,
            'document' => $this->faker->text('200'),
            'created_by' => 1,
            'created_at' => $dt,
        );

        $dt = $this->faker->dateTime;
        $dt->setTime(0, 0, 0);
        $arr[] = array(
            'report_id' => 2,
            'document' => $this->faker->text('200'),
            'created_by' => 1,
            'created_at' => $dt,
        );
        return $arr;
    }

    public function create()
    {
        $dt = $this->faker->dateTime;
        $dt->setTime(0, 0, 0);
        return array(
            'report_id' => 3,
            'document' => $this->faker->text('200'),
            'created_by' => 1,
            'created_at' => $dt,
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
