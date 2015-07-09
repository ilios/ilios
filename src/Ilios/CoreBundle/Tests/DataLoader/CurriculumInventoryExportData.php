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
            'createdBy' => 1,
            'createdOn' => $dt->format('c'),
        );

        $dt = $this->faker->dateTime;
        $dt->setTime(0, 0, 0);
        $arr[] = array(
            'report_id' => 1,
            'document' => $this->faker->text('200'),
            'createdBy' => 1,
            'createdOn' => $dt->format('c'),
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
            'createdBy' => 1,
            'createdOn' => $dt->format('c'),
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
