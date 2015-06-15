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
            'owningSchool' => "1",
            'objectives' => [],
            'children' => [],
            'aamcPcrses' => ['aamc-pcrs-comp-c0101'],
            'programYears' => []
        );

        $arr[] = array(
            'id' => 2,
            'title' => $this->faker->text,
            'owningSchool' => "1",
            'objectives' => [],
            'children' => [],
            'aamcPcrses' => ['aamc-pcrs-comp-c0101', 'aamc-pcrs-comp-c0102'],
            'programYears' => []
        );

        $arr[] = array(
            'id' => 3,
            'title' => $this->faker->text,
            'owningSchool' => "1",
            'objectives' => [],
            'children' => [],
            'aamcPcrses' => ['aamc-pcrs-comp-c0102'],
            'programYears' => []
        );



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
