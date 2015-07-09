<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class CurriculumInventorySequenceData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'report_id' => 1,
            'description' => $this->faker->text(100),
        );

        $arr[] = array(
            'report_id' => 2,
            'description' => $this->faker->text(100),
        );


        return $arr;
    }

    public function create()
    {
        return array(
            'report_id' => 3,
            'description' => $this->faker->text(100),
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
