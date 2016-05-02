<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

/**
 * Class AamcResourceTypeData
 * @package Ilios\CoreBundle\Tests\DataLoader
 */
class AamcResourceTypeData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'title' => $this->faker->text(100),
            'description' => $this->faker->text,
            'terms' => ['1','2'],
        );

        $arr[] = array(
            'id' => 2,
            'title' => $this->faker->text(100),
            'description' => $this->faker->text,
            'terms' => ['2', '3'],
        );

        $arr[] = array(
            'id' => 3,
            'title' =>$this->faker->text(100),
            'description' => $this->faker->text,
            'terms' => [],
        );


        return $arr;
    }

    public function create()
    {
        return [
            'id' => 4,
            'title' => $this->faker->text(100),
            'description' => $this->faker->text,
            'terms' => [],
        ];
    }

    public function createInvalid()
    {
        return [];
    }
}
