<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class UserRoleData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'title' => "Course Director",
            'users' => ['4136']
        );

        $arr[] = array(
            'id' => 2,
            'title' => "Developer",
            'users' => []
        );

        $arr[] = array(
            'id' => 3,
            'title' => "Faculty",
            'users' => ['4136']
        );

        $arr[] = array(
            'id' => 4,
            'title' => "Student",
            'users' => [
                '11256',
                '11257',
                '11258',
                '11259',
                '11260',
                '11261',
                '11262',
                '11263',
                '11264',
                '11265',
                '11266',
                '11267',
                '11268',
                '11269',
                '11270',
                '11272',
            ]
        );

        $arr[] = array(
            'id' => 5,
            'title' => "Public",
            'users' => []
        );

        $arr[] = array(
            'id' => 9,
            'title' => "Former Student",
            'users' => ['4136']
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
