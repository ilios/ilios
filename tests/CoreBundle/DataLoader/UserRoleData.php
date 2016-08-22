<?php

namespace Tests\CoreBundle\DataLoader;

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

        $arr[] = array(
            'id' => 2,
            'title' => 'Something Else',
            'users' => ['3'],
        );


        return $arr;
    }

    public function create()
    {
        $arr = array();

        $arr[] = array(
            'id' => 3,
            'title' => $this->faker->text(10)
        );

        return $arr[0];
    }

    public function createInvalid()
    {
        return [];
    }
}
