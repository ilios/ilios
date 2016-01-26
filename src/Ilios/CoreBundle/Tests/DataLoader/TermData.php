<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class TermData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $arr[] = array(
            'id' => 1,
            'title' => $this->faker->text(100),
            'vocabulary' => '1',
            'parent' => null,
            'children' => ['1', '2'],
            'courses' => ['2'],
            'programYears' => ["2"],
            'sessions' => ['2'],
        );
        $arr[] = array(
            'id' => 2,
            'title' => $this->faker->text(100),
            'vocabulary' => '1',
            'parent' => '1',
            'children' => [],
            'courses' => [],
            'programYears' => [],
            'sessions' => ['1']
        );
        $arr[] = array(
            'id' => 3,
            'title' => $this->faker->text(100),
            'vocabulary' => '1',
            'parent' => '1',
            'children' => [],
            'courses' => ['4'],
            'programYears' => [],
            'sessions' => ['3']
        );

        $arr[] = array(
            'id' => 4,
            'title' => $this->faker->text(100),
            'vocabulary' => '2',
            'parent' => null,
            'children' => [],
            'courses' => ['2'],
            'programYears' => ["2"],
            'sessions' => ['2'],
        );
        $arr[] = array(
            'id' => 5,
            'title' => $this->faker->text(100),
            'vocabulary' => '2',
            'parent' => null,
            'children' => [],
            'courses' => [],
            'programYears' => [],
            'sessions' => ['1']
        );
        $arr[] = array(
            'id' => 6,
            'title' => $this->faker->text(100),
            'vocabulary' => '2',
            'parent' => null,
            'children' => [],
            'courses' => ['4'],
            'programYears' => [],
            'sessions' => ['3']
        );

        return $arr;
    }

    public function create()
    {
        return [
            'id' => 7,
            'title' => $this->faker->text(100),
            'vocabulary' => '2',
            'parent' => null,
            'children' => [],
            'courses' => ['4'],
            'programYears' => [],
            'sessions' => ['3']
        ];
    }

    public function createInvalid()
    {
        return [
            'vocabulary' => 11
        ];
    }
}
