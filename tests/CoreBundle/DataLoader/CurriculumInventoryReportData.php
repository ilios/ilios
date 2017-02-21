<?php

namespace Tests\CoreBundle\DataLoader;

class CurriculumInventoryReportData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        
        $dt = $this->faker->dateTime;
        $dt->setTime(0, 0, 0);
        $arr[] = array(
            'id' => 1,
            'program' => '1',
            'sequence' => '1',
            'year' => $this->faker->date('Y'),
            'name' => $this->faker->text(100),
            'description' => $this->faker->text(200),
            'startDate' => $dt->format('c'),
            'endDate' => $dt->format('c'),
            'sequenceBlocks' => ['1', '2', '3', '4', '5'],
            'academicLevels' => ['1', '2'],
            'export' => ''
        );
        
        $arr[] = array(
            'id' => 2,
            'export' => '1',
            'program' => '1',
            'sequence' => '2',
            'year' => $this->faker->date('Y'),
            'name' => $this->faker->text(100),
            'description' => $this->faker->text(200),
            'startDate' => $dt->format('c'),
            'endDate' => $dt->format('c'),
            'sequenceBlocks' => [],
            'academicLevels' => [],
        );
        $arr[] = array(
            'id' => 3,
            'export' => '2',
            'program' => '1',
            'year' => $this->faker->date('Y'),
            'name' => $this->faker->text(100),
            'description' => $this->faker->text(200),
            'startDate' => $dt->format('c'),
            'endDate' => $dt->format('c'),
            'sequenceBlocks' => [],
            'academicLevels' => [],
        );

        return $arr;
    }

    public function create()
    {
        $dt = $this->faker->dateTime;
        $dt->setTime(0, 0, 0);
        return array(
            'id' => 4,
            'program' => '2',
            'year' => (int) $this->faker->date('Y'),
            'name' => $this->faker->text(100),
            'description' => $this->faker->text(200),
            'startDate' => $dt->format('c'),
            'endDate' => $dt->format('c'),
            'sequenceBlocks' => [],
            'academicLevels' => [],
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
