<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class TopicData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $arr[] = array(
            'id' => 1,
            'title' => $this->faker->text(100),
            'school' => '1',
            'courses' => ['2'],
            'programYears' => ["2"],
            'sessions' => ['2']
        );
        $arr[] = array(
            'id' => 2,
            'title' => $this->faker->text(100),
            'school' => '1',
            'courses' => [],
            'programYears' => [],
            'sessions' => ['1']
        );
        $arr[] = array(
            'id' => 3,
            'title' => $this->faker->text(100),
            'school' => '1',
            'courses' => ['4'],
            'programYears' => [],
            'sessions' => ['3']
        );


        return $arr;
    }

    public function create()
    {
        return [
            'id' => 4,
            'title' => $this->faker->text(100),
            'school' => '1',
            'courses' => [],
            'programYears' => [],
            'sessions' => ['1']
        ];
    }

    public function createInvalid()
    {
        return [
            'school' => 11
        ];
    }
}
