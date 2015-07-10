<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class CurriculumInventoryAcademicLevelData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $arr[] = array(
            'id' => 1,
            'name' => $this->faker->text(10),
            'description' => $this->faker->text(100),
            'level' => $this->faker->numberBetween(1, 10),
            'report_id' => 1,
        );
        $arr[] = array(
            'id' => 2,
            'name' => $this->faker->text(10),
            'description' => $this->faker->text(100),
            'level' => $this->faker->numberBetween(1, 10),
            'report_id' => 2,
        );

        return $arr;
    }

    public function create()
    {
        $arr = array(
            'id' => 3,
            'name' => $this->faker->text(10),
            'description' => $this->faker->text(100),
            'level' => $this->faker->numberBetween(1, 10),
            'report_id' => 3,
        );
        return $arr;
    }

    public function createInvalid()
    {
        return [];
    }
}
