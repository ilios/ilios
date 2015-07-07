<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class PublishEventData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'machineIp' => $this->faker->ipv4,
            'sessions' => [1],
            'courses' => []
        );


        return $arr;
    }

    public function create()
    {
        return [];
    }

    public function createInvalid()
    {
        return [];
    }
}
