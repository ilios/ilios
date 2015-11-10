<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class UserRoleData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'title' => 'Developer',
            'users' => ['1','2'],
        );


        return $arr;
    }

    public function create()
    {
        $arr = array();

        $arr[] = array(
            'id' => 2,
            'title' => $this->faker->text(10)
        );

        return $arr[0];
    }

    public function createInvalid()
    {
        return [];
    }
}
