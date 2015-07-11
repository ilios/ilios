<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class CurriculumInventoryExportData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'report' => '1',
            'document' => $this->faker->text('200'),
            'createdBy' => '1',
        );

        $arr[] = array(
            'id' => 2,
            'report' => '2',
            'document' => $this->faker->text('200'),
            'createdBy' => '1',
        );
        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 2,
            'report' => 1,
            'createdBy' => '1',
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
