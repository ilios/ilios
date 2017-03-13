<?php

namespace Tests\CoreBundle\DataLoader;

class SchoolConfigData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $arr[] = array(
            'id' => 1,
            'name' => '1' . $this->faker->text(50),
            'value' => $this->faker->text(100),
            'school' => '1',
        );
        $arr[] = array(
            'id' => 2,
            'name' => 'second config',
            'value' => $this->faker->text(100),
            'school' => '1',
        );
        $arr[] = array(
            'id' => 3,
            'name' => '3' . $this->faker->text(50),
            'value' => 'third value',
            'school' => '2',
        );
        return $arr;
    }

    public function create()
    {
        return [
            'id' => 4,
            'name' => '4' . $this->faker->text(50),
            'value' => $this->faker->text(100),
            'school' => '1',
        ];
    }

    public function createInvalid()
    {
        return [
            'name' => null,
        ];
    }
}
