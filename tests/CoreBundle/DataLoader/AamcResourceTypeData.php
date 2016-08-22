<?php

namespace Tests\CoreBundle\DataLoader;

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
            'id' => 'RE001',
            'title' => $this->faker->text(100),
            'description' => $this->faker->text,
            'terms' => ['1','2'],
        );

        $arr[] = array(
            'id' => 'RE002',
            'title' => $this->faker->text(100),
            'description' => $this->faker->text,
            'terms' => ['2', '3'],
        );

        $arr[] = array(
            'id' => 'RE003',
            'title' =>$this->faker->text(100),
            'description' => $this->faker->text,
            'terms' => [],
        );


        return $arr;
    }

    public function create()
    {
        return [
            'id' => 'RE004',
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
