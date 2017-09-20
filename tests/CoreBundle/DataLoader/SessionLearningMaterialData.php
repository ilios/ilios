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
            'startDate' => null,
            'endDate' => null,
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
            'startDate' => null,
            'endDate' => null,
        );

        $arr[] = array(
            'id' => 3,
            'required' => false,
            'publicNotes' => true,
            'notes' => 'third slm',
            'session' => '3',
            'learningMaterial' => '5',
            'meshDescriptors' => ['abc2'],
            'position' => 0,
            'startDate' => date_format(new \DateTime('2 days ago'), 'c'),
            'endDate' => null,
        );

        $arr[] = array(
            'id' => 4,
            'required' => false,
            'publicNotes' => true,
            'notes' => 'fourth slm',
            'session' => '3',
            'learningMaterial' => '6',
            'meshDescriptors' => ['abc2'],
            'position' => 0,
            'startDate' => date_format(new \DateTime('+2 days'), 'c'),
            'endDate' => null,
        );

        $arr[] = array(
            'id' => 5,
            'required' => false,
            'publicNotes' => true,
            'notes' => 'fifth slm',
            'session' => '3',
            'learningMaterial' => '7',
            'meshDescriptors' => ['abc2'],
            'position' => 0,
            'startDate' => null,
            'endDate' => date_format(new \DateTime('+ 2 days'), 'c'),
        );

        $arr[] = array(
            'id' => 6,
            'required' => false,
            'publicNotes' => true,
            'notes' => 'sixth slm',
            'session' => '3',
            'learningMaterial' => '8',
            'meshDescriptors' => ['abc2'],
            'position' => 0,
            'startDate' => null,
            'endDate' => date_format(new \DateTime('2 days ago'), 'c'),
        );

        $arr[] = array(
            'id' => 7,
            'required' => false,
            'publicNotes' => true,
            'notes' => 'seventh slm',
            'session' => '3',
            'learningMaterial' => '9',
            'meshDescriptors' => ['abc2'],
            'position' => 0,
            'startDate' => date_format(new \DateTime('2 days ago'), 'c'),
            'endDate' => date_format(new \DateTime('+2 days'), 'c'),
        );

        $arr[] = array(
            'id' => 8,
            'required' => false,
            'publicNotes' => true,
            'notes' => 'eighth slm',
            'session' => '3',
            'learningMaterial' => '10',
            'meshDescriptors' => ['abc2'],
            'position' => 0,
            'startDate' => date_format(new \DateTime('4 days ago'), 'c'),
            'endDate' => date_format(new \DateTime('2 days ago'), 'c'),
        );

        return $arr;
    }

    public function create()
    {
        return [
            'id' => 9,
            'required' => false,
            'notes' => $this->faker->text,
            'publicNotes' => false,
            'session' => '1',
            'learningMaterial' => '2',
            'meshDescriptors' => [],
            'position' => 0,
            'startDate' => null,
            'endDate' => null,
        ];
    }

    public function createInvalid()
    {
        return [
            'session' => 11
        ];
    }
}
