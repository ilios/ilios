<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class CurriculumInventoryAcademicLevelData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $arr = array(
            'id' => 1,
            'name' => $this->faker->string(10)
        );
        // $arr[] = array(
        //     'id' => 81,
        //     'report' => "9",
        //     'sequenceBlocks' => ['16','18']
        // );

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
