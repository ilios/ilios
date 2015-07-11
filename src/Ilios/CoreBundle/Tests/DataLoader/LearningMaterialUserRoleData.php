<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class LearningMaterialUserRoleData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'title' => $this->faker->text(10),
            'learningMaterials' => ['1']
        );

        $arr[] = array(
            'id' => 2,
            'title' => $this->faker->text(10),
            'learningMaterials' => ['2']
        );

        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 3,
            'title' => $this->faker->text(10),
            'learningMaterials' => ['1', '2']
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
