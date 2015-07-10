<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class SessionDescriptionData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $arr[] = array(
            'session' => '1',
            'description' => $this->faker->text
        );

        return $arr;
    }

    public function create()
    {
        return array(
            'session' => 2,
            'description' => $this->faker->text
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
