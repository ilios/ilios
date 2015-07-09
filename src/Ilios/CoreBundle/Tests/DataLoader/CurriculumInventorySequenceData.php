<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class CurriculumInventorySequenceData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'report' => 1,
            'description' => $this->faker->text(100),
        );

        $arr[] = array(
            'report' => 2,
            'description' => $this->faker->text(100),
        );


        return $arr;
    }

    public function create()
    {
        return array(
            'report' => 3,
            'description' => $this->faker->text(100),
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
