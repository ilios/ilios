<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class CurriculumInventoryExportData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $arr[] = array(
            'report_id' => 1,
            'program_id' => '1',
            'year' => $this->faker->date('Y'),
            'name' => $this->faker->text(100),
            'description' => $this->faker->text(200),
            'start_date' => $this->faker->date('Y-m-d'),
            'end_date' => $this->faker->date('Y-m-d'),
        );

        return $arr;
    }

    public function create()
    {
        return array(
            'program_id' => '1',
            'year' => $this->faker->date('Y'),
            'name' => $this->faker->text(100),
            'description' => $this->faker->text(200),
            'start_date' => $this->faker->date('Y-m-d'),
            'end_date' => $this->faker->date('Y-m-d')
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
