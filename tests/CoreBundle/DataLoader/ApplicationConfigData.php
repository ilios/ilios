<?php

namespace Tests\CoreBundle\DataLoader;

class ApplicationConfigData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $arr[] = array(
            'id' => 1,
            'name' => '1' . $this->faker->text(50),
            'value' => $this->faker->text(100),
        );
        $arr[] = array(
            'id' => 2,
            'name' => 'second name',
            'value' => $this->faker->text(100),
        );
        $arr[] = array(
            'id' => 3,
            'name' => '2' . $this->faker->text(50),
            'value' => 'third value',
        );

        return $arr;
    }

    public function create()
    {
        return [
            'id' => 4,
            'name' => '4' . $this->faker->text(50),
            'value' => $this->faker->text(100),
        ];
    }

    public function createInvalid()
    {
        return [
            'id' => 'dsfdsaf'
        ];
    }
}
