<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class AlertChangeTypeData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'title' => $this->faker->text(25),
            'alerts' => ['1']
        );

        $arr[] = array(
            'id' => 2,
            'title' => $this->faker->text(25),
            'alerts' => []
        );

        return $arr;
    }

    public function create()
    {
        return [
            'id' => 3,
            'title' => $this->faker->text(10),
            'alerts' => []
        ];
    }

    public function createInvalid()
    {
        return [
            'id' => $this->faker->text,
            'alerts' => [424524]
        ];
    }
}
