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
            'name' => '2' . $this->faker->text(50),
            'value' => $this->faker->text(100),
        );

        return $arr;
    }

    public function create()
    {
        return [
            'id' => 3,
            'name' => '3' . $this->faker->text(50),
            'value' => $this->faker->text(100),
        ];
    }

    public function createInvalid()
    {
        return [
            'name' => null,
        ];
    }
}
