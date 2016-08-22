<?php

namespace Tests\CoreBundle\DataLoader;

class LearningMaterialUserRoleData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'title' => $this->faker->text(10)
        );

        $arr[] = array(
            'id' => 2,
            'title' => $this->faker->text(10)
        );

        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 3,
            'title' => $this->faker->text(10)
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
