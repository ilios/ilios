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
            'meshDescriptors' => ['abc1'],
            'position' => 0,
            'startDate' => null,
            'endDate' => null,
        );

        $arr[] = array(
            'id' => 2,
            'required' => false,
            'publicNotes' => true,
            'notes' => $this->faker->text,
            'course' => "1",
            'learningMaterial' => "2",
            'meshDescriptors' => [],
            'position' => 1,
            'startDate' => null,
            'endDate' => null,
        );

        $arr[] = array(
            'id' => 3,
            'required' => true,
            'publicNotes' => false,
            'notes' => 'third note',
            'course' => "4",
            'learningMaterial' => "1",
            'meshDescriptors' => ['abc1'],
            'position' => 0,
            'startDate' => null,
            'endDate' => null,
        );

        $arr[] = array(
            'id' => 4,
            'required' => true,
            'publicNotes' => true,
            'notes' => $this->faker->text,
            'course' => "1",
            'learningMaterial' => "3",
            'meshDescriptors' => [],
            'position' => 2,
            'startDate' => null,
            'endDate' => null,
        );

        $arr[] = array(
            'id' => 5,
            'required' => true,
            'publicNotes' => true,
            'notes' => $this->faker->text,
            'course' => "1",
            'learningMaterial' => "5",
            'meshDescriptors' => [],
            'position' => 3,
            'startDate' => date_format(new \DateTime('2 days ago'), 'c'),
            'endDate' => null,
        );

        $arr[] = array(
            'id' => 6,
            'required' => true,
            'publicNotes' => true,
            'notes' => $this->faker->text,
            'course' => "1",
            'learningMaterial' => "6",
            'meshDescriptors' => [],
            'position' => 4,
            'startDate' => date_format(new \DateTime('+2 days'), 'c'),
            'endDate' => null
        );

        $arr[] = array(
            'id' => 7,
            'required' => true,
            'publicNotes' => true,
            'notes' => $this->faker->text,
            'course' => "1",
            'learningMaterial' => "7",
            'meshDescriptors' => [],
            'position' => 5,
            'startDate' => null,
            'endDate' => date_format(new \DateTime('+2 days'), 'c'),
        );

        $arr[] = array(
            'id' => 8,
            'required' => true,
            'publicNotes' => true,
            'notes' => $this->faker->text,
            'course' => "1",
            'learningMaterial' => "8",
            'meshDescriptors' => [],
            'position' => 6,
            'startDate' => null,
            'endDate' => date_format(new \DateTime('2 days ago'), 'c'),
        );

        $arr[] = array(
            'id' => 9,
            'required' => true,
            'publicNotes' => true,
            'notes' => $this->faker->text,
            'course' => "1",
            'learningMaterial' => "9",
            'meshDescriptors' => [],
            'position' => 7,
            'startDate' => date_format(new \DateTime('2 days ago'), 'c'),
            'endDate' => date_format(new \DateTime('+2 days'), 'c'),
        );

        $arr[] = array(
            'id' => 10,
            'required' => true,
            'publicNotes' => true,
            'notes' => $this->faker->text,
            'course' => "1",
            'learningMaterial' => "10",
            'meshDescriptors' => [],
            'position' => 8,
            'startDate' => date_format(new \DateTime('4 days ago'), 'c'),
            'endDate' => date_format(new \DateTime('2 days ago'), 'c'),
        );

        return $arr;
    }

    public function create()
    {
        return [
            'id' => 11,
            'required' => true,
            'publicNotes' => true,
            'notes' => $this->faker->text,
            'course' => "1",
            'learningMaterial' => "2",
            'meshDescriptors' => [],
            'position' => 0,
            'startDate' => null,
            'endDate' => null,
        ];
    }

    public function createInvalid()
    {
        return [];
    }
}
