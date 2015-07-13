<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class LearningMaterialData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => 1,
            'title' => $this->faker->text(30),
            'description' => $this->faker->text,
            'originalAuthor' => $this->faker->name,
            'userRole' => "1",
            'status' => "1",
            'owningUser' => "1",
            'sessionLearningMaterials' => [1],
            'courseLearningMaterials' => [1],
            'citation' => $this->faker->text
        );

        $arr[] = array(
            'id' => 2,
            'title' => $this->faker->text(30),
            'description' => $this->faker->text,
            'originalAuthor' => $this->faker->name,
            'userRole' => "2",
            'status' => "1",
            'owningUser' => "1",
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [2],
            'citation' => $this->faker->text
        );


        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 3,
            'title' => $this->faker->text(30),
            'description' => $this->faker->text,
            'originalAuthor' => $this->faker->name,
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [],
            'citation' => $this->faker->text,
            'userRole' => "2",
            'status' => "1",
            'owningUser' => "1",
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
