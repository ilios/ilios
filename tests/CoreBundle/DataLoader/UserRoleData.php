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
        );

        $arr[] = array(
            'id' => 2,
            'title' => 'Something Else',
        );

        $arr[] = array(
            'id' => 3,
            'title' => 'Course Director',
        );


        return $arr;
    }

    public function create()
    {
        $arr = array();

        $arr[] = array(
            'id' => 4,
            'title' => $this->faker->text(10)
        );

        return $arr[0];
    }

    public function createInvalid()
    {
        return [];
    }
}
