<?php

namespace Tests\CoreBundle\DataLoader;

class PendingUserUpdateData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'type' => $this->faker->text(15),
            'property' => 'first property',
            'value' => $this->faker->text(25),
            'user' => '1',
        );


        $arr[] = array(
            'id' => 2,
            'type' => 'second type',
            'property' => $this->faker->text(5),
            'value' => 'second value',
            'user' => '4',
        );

        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 3,
            'type' => $this->faker->text(15),
            'property' => $this->faker->text(5),
            'value' => $this->faker->text(25),
            'user' => '1',
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
