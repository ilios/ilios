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
            'id' => 'RE01',
            'title' => $this->faker->text(100),
            'description' => $this->faker->text,
            'terms' => ['1','2'],
        );

        $arr[] = array(
            'id' => 'RE02',
            'title' => $this->faker->text(100),
            'description' => $this->faker->text,
            'terms' => ['2', '3'],
        );

        $arr[] = array(
            'id' => 'RE03',
            'title' =>$this->faker->text(100),
            'description' => $this->faker->text,
            'terms' => [],
        );


        return $arr;
    }

    public function create()
    {
        return [
            'id' => 'RE04',
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
