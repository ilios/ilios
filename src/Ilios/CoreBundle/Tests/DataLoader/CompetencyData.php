<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class CompetencyData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'title' => $this->faker->text,
            'school' => "1",
            'objectives' => ["1", "2"],
            'children' => [3],
            'aamcPcrses' => ['aamc-pcrs-comp-c0101'],
            'programYears' => []
        );

        $arr[] = array(
            'id' => 2,
            'title' => $this->faker->text,
            'school' => "1",
            'objectives' => ["3"],
            'children' => [],
            'aamcPcrses' => ['aamc-pcrs-comp-c0101', 'aamc-pcrs-comp-c0102'],
            'programYears' => []
        );

        $arr[] = array(
            'id' => 3,
            'title' => $this->faker->text,
            'school' => "1",
            'objectives' => [],
            'parent' => "1",
            'children' => [],
            'aamcPcrses' => ['aamc-pcrs-comp-c0102'],
            'programYears' => []
        );



        return $arr;
    }

    public function create()
    {
        return [
            'id' => 4,
            'title' => $this->faker->text,
            'school' => "1",
            'objectives' => [],
            'parent' => "1",
            'children' => [],
            'aamcPcrses' => ['aamc-pcrs-comp-c0102'],
            'programYears' => ['1']
        ];
    }

    public function createInvalid()
    {
        return [
            'school' => 11
        ];
    }
}
