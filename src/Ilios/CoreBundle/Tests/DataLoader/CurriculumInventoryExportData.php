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
            'report' => 1,
            'document' => $this->faker->text('200'),
            'createdBy' => 1,
            'createdAt' => $dt,
        );

        $dt = $this->faker->dateTime;
        $dt->setTime(0, 0, 0);
        $arr[] = array(
            'report' => 2,
            'document' => $this->faker->text('200'),
            'createdBy' => 1,
            'createdAt' => $dt,
        );
        return $arr;
    }

    public function create()
    {
        $dt = $this->faker->dateTime;
        $dt->setTime(0, 0, 0);
        return array(
            'report' => 3,
            'document' => $this->faker->text('200'),
            'createdBy' => 1,
            'createdAt' => $dt,
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
