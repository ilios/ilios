<?php

namespace Tests\CoreBundle\DataLoader;

class CurriculumInventoryAcademicLevelData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $arr[] = array(
            'id' => 1,
            'name' => 'Year 1',
            'description' => $this->faker->text(100),
            'level' => 1,
            'report' => '1',
            'sequenceBlocks' => ['1'],
        );
        $arr[] = array(
            'id' => 2,
            'name' => 'Year 2',
            'description' => $this->faker->text(100),
            'level' => 2,
            'report' => '1',
            'sequenceBlocks' => ['2', '3', '4', '5'],
        );
        return $arr;
    }

    public function create()
    {
        $arr = array(
            'id' => 3,
            'name' => $this->faker->text(10),
            'description' => $this->faker->text(100),
            'level' => 2,
            'report' => '1',
            'sequenceBlocks' => []
        );
        return $arr;
    }

    public function createInvalid()
    {
        return [];
    }
}
