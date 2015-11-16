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
            'annotation' => 'annotation' . $this->faker->text,
            'courses' => ["1"],
            'objectives' => ["4", "5"],
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
            'annotation' => 'annotation' . $this->faker->text,
            'courses' => [],
            'objectives' => [],
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [],
            'sessions' => ["3"],
            'concepts' => [],
            'qualifiers' => [],
            'trees' => [],
            'previousIndexing' => '2'
        );
        $arr[] = array(
            'id' => 'abc3',
            'name' => 'desc' . $this->faker->text,
            'annotation' => 'annotation' . $this->faker->text,
            'courses' => [],
            'objectives' => ["6"],
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [],
            'sessions' => [],
            'concepts' => [],
            'qualifiers' => [],
            'trees' => []
        );

        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 'abc4',
            'name' => $this->faker->text,
            'annotation' => $this->faker->text,
            'courses' => ['1'],
            'objectives' => ['1'],
            'sessionLearningMaterials' => ['1'],
            'courseLearningMaterials' => ['1'],
            'sessions' => ['1'],
            'concepts' => ['1'],
            'qualifiers' => ['1'],
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
