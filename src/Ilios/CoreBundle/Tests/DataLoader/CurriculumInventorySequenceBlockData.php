<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockInterface;

class CurriculumInventorySequenceBlockData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $dt = $this->faker->dateTime;
        $dt->setTime(0, 0, 0);
        $arr[] = array(
            'id' => 1,
            'title' => $this->faker->text(10),
            'report' => '1',
            'childSequenceOrder' => 1,
            'orderInSequence' => 0,
            'academicLevel' => 1,
            'minimum' => 1,
            'maximum' => 1,
            'duration' => 1,
            'required' => CurriculumInventorySequenceBlockInterface::REQUIRED,
            'startDate' => $dt->format('c'),
            'endDate' => $dt->format('c'),
            'children' => ['2'],
            'sessions' => ['1']
        );
        $arr[] = array(
            'id' => 2,
            'title' => $this->faker->text(10),
            'report' => '1',
            'childSequenceOrder' => 1,
            'orderInSequence' => 0,
            'academicLevel' => 2,
            'minimum' => 1,
            'maximum' => 1,
            'duration' => 1,
            'required' => CurriculumInventorySequenceBlockInterface::OPTIONAL,
            'startDate' => $dt->format('c'),
            'endDate' => $dt->format('c'),
            'children' => [],
            'sessions' => [],
            'parent' => '1'
        );


        return $arr;
    }

    public function create()
    {
        $dt = $this->faker->dateTime;
        $dt->setTime(0, 0, 0);
        return array(
            'id' => 3,
            'title' => $this->faker->text(10),
            'report' => '1',
            'childSequenceOrder' => 1,
            'orderInSequence' => 0,
            'academicLevel' => 2,
            'minimum' => 1,
            'maximum' => 1,
            'duration' => 1,
            'required' => CurriculumInventorySequenceBlockInterface::REQUIRED_IN_TRACK,
            'startDate' => $dt->format('c'),
            'endDate' => $dt->format('c'),
            'children' => [],
            'sessions' => [],
            'parent' => '1'
        );
    }

    public function createInvalid()
    {
        return array(
            'id' => 4,
        );
    }
}
