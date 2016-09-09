<?php

namespace Tests\CoreBundle\DataLoader;

class CourseLearningMaterialData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'required' => true,
            'publicNotes' => true,
            'notes' => $this->faker->text,
            'course' => "1",
            'learningMaterial' => "1",
            'meshDescriptors' => []
        );

        $arr[] = array(
            'id' => 2,
            'required' => true,
            'publicNotes' => true,
            'notes' => $this->faker->text,
            'course' => "1",
            'learningMaterial' => "2",
            'meshDescriptors' => []
        );

        $arr[] = array(
            'id' => 3,
            'required' => true,
            'publicNotes' => true,
            'notes' => $this->faker->text,
            'course' => "4",
            'learningMaterial' => "1",
            'meshDescriptors' => ['abc1']
        );

        $arr[] = array(
            'id' => 4,
            'required' => true,
            'publicNotes' => true,
            'notes' => $this->faker->text,
            'course' => "1",
            'learningMaterial' => "3",
            'meshDescriptors' => []
        );


        return $arr;
    }

    public function create()
    {
        return [
            'id' => 5,
            'required' => true,
            'publicNotes' => true,
            'notes' => $this->faker->text,
            'course' => "1",
            'learningMaterial' => "2",
            'meshDescriptors' => []
        ];
    }

    public function createInvalid()
    {
        return [];
    }
}
