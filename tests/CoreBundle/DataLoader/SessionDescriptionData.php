<?php

namespace Tests\CoreBundle\DataLoader;

class SessionDescriptionData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $arr[] = array(
            'id' => 1,
            'session' => '1',
            'description' => $this->faker->text
        );
        $arr[] = array(
            'id' => 2,
            'session' => '2',
            'description' => 'second description'
        );

        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 3,
            'session' => 3,
            'description' => $this->faker->text
        );
    }

    public function createInvalid()
    {
        return [
            'session' => 11
        ];
    }
}
