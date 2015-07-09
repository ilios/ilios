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
            'id' => 10,
            'name' => $this->faker->text(10),
            'description' => $this->faker->text(100),
            'level' => $this->faker->numberBetween(1, 10),
            'report_id' => 10,
        );

        return $arr;
    }

    public function create()
    {
        $arr = array(
            'name' => $this->faker->string(10),
            'description' => $this->faker->text(100),
            'level' => $this->faker->numberBetween(1, 10),
            'report_id' => 1,
        );
        return $arr;
    }

    public function createInvalid()
    {
        return [];
    }
}
