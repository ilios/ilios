<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class SessionLearningMaterialData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'required' => false,
            'publicNotes' => false,
            'notes' => $this->faker->text,
            'session' => '1',
            'learningMaterial' => '1',
            'meshDescriptors' => []
        );

        $arr[] = array(
            'id' => 2,
            'required' => false,
            'publicNotes' => false,
            'notes' => $this->faker->text,
            'session' => '3',
            'learningMaterial' => '3',
            'meshDescriptors' => []
        );


        return $arr;
    }

    public function create()
    {

        $arr = array();

        return array(
          'id' => 3,
          'required' => false,
          'notes' => $this->faker->text,
          'publicNotes' => false,
          'session' => '1',
          'meshDescriptors' => []
        );

        return $arr;

    }

    public function createInvalid()
    {
        return [
            'session' => 11
        ];
    }
}
