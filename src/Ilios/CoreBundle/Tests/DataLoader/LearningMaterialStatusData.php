<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class LearningMaterialStatusData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'title' => $this->faker->text(10),
            'learningMaterials' => ['1', '2']
        );
        $arr[] = array(
            'id' => 2,
            'title' => $this->faker->text(10),
            'learningMaterials' => []
        );


        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 3,
            'title' => $this->faker->text(10),
            'learningMaterials' => []
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
