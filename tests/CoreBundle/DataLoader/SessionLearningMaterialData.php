<?php

namespace Tests\CoreBundle\DataLoader;

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
            'meshDescriptors' => ['abc1']
        );

        $arr[] = array(
            'id' => 2,
            'required' => false,
            'publicNotes' => false,
            'notes' => $this->faker->text,
            'session' => '3',
            'learningMaterial' => '3',
            'meshDescriptors' => ['abc2']
        );


        return $arr;
    }

    public function create()
    {
        return array(
          'id' => 3,
          'required' => false,
          'notes' => $this->faker->text,
          'publicNotes' => false,
          'session' => '1',
          'learningMaterial' => '2',
          'meshDescriptors' => []
        );
    }

    public function createInvalid()
    {
        return [
            'session' => 11
        ];
    }
}
