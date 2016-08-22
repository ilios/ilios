<?php

namespace Tests\CoreBundle\DataLoader;

class CurriculumInventoryExportData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'report' => '2',
            'document' => $this->faker->text('200'),
            'createdBy' => '1',
        );

        $arr[] = array(
            'id' => 2,
            'report' => '3',
            'document' => $this->faker->text('200'),
            'createdBy' => '1',
        );
        return $arr;
    }

    public function create()
    {
        return [
            'report' => '1',
        ];
    }

    public function createInvalid()
    {
        return [];
    }
}
