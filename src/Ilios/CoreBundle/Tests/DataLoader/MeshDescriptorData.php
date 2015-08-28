<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class MeshDescriptorData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();
        $arr[] = array(
            'id' => 'abc1',
            'name' => 'desc' . $this->faker->text,
            'annotation' => $this->faker->text,
            'courses' => [],
            'objectives' => [],
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [],
            'sessions' => [],
            'concepts' => ['1', '2'],
            'qualifiers' => ['1', '2'],
            'trees' => ['1', '2'],
            'previousIndexing' => '1'
        );
        $arr[] = array(
            'id' => 'abc2',
            'name' => 'desc' . $this->faker->text,
            'annotation' => $this->faker->text,
            'courses' => [],
            'objectives' => [],
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [],
            'sessions' => [],
            'concepts' => [],
            'qualifiers' => [],
            'trees' => [],
            'previousIndexing' => '2'
        );

        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 'abc3',
            'name' => $this->faker->text,
            'annotation' => $this->faker->text,
            'courses' => [],
            'objectives' => [],
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [],
            'sessions' => [],
            'concepts' => [],
            'qualifiers' => [],
            'trees' => [],
        );
    }

    public function createInvalid()
    {
        return array(
            'id' => 'bad'
        );
    }
}
