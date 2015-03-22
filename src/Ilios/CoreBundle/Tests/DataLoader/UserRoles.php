<?php
namespace Ilios\CoreBundle\Tests\DataLoader;

class UserRoles
{
    public function get()
    {
        $arr = array();

        $arr[1] = array(
            'id' => 1,
            'title' => "Course Director",
            'users' => []
        );

        $arr[2] = array(
            'id' => 2,
            'title' => "Developer",
            'users' => []
        );

        $arr[3] = array(
            'id' => 3,
            'title' => "Faculty",
            'users' => [],
        );

        $arr[4] = array(
            'id' => 4,
            'title' => "Student",
            'users' => []
        );

        $arr[5] = array(
            'id' => 5,
            'title' => "Public",
            'users' => []
        );

        $arr[9] = array(
            'id' => 9,
            'title' => "Former Student",
            'users' => []
        );

        return $arr;
    }
}
