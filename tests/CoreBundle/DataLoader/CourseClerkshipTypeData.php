<?php

namespace Tests\CoreBundle\DataLoader;

class CourseClerkshipTypeData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'title' => $this->faker->text(10),
            'courses' => ['1', '2']
        );

        $arr[] = array(
            'id' => 2,
            'title' => 'second clerkship type',
            'courses' => []
        );


        return $arr;
    }

    public function create()
    {
        return [
            'id' => 3,
            'title' => $this->faker->text(10),
            'courses' => []
        ];
    }

    public function createInvalid()
    {
        return [];
    }
}
