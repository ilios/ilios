<?php

namespace Tests\CoreBundle\DataLoader;

class SessionLearningMaterialData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'required' => true,
            'publicNotes' => false,
            'notes' => $this->faker->text,
            'session' => '1',
            'learningMaterial' => '1',
            'meshDescriptors' => ['abc1'],
            'position' => 1,
        );

        $arr[] = array(
            'id' => 2,
            'required' => false,
            'publicNotes' => true,
            'notes' => 'second slm',
            'session' => '3',
            'learningMaterial' => '3',
            'meshDescriptors' => ['abc2'],
            'position' => 0,
        );


        return $arr;
    }

    public function create()
    {
        return [
            'id' => 3,
            'required' => false,
            'notes' => $this->faker->text,
            'publicNotes' => false,
            'session' => '1',
            'learningMaterial' => '2',
            'meshDescriptors' => [],
            'position' => 0,
        ];
    }

    public function createInvalid()
    {
        return [
            'session' => 11
        ];
    }
}
