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
            'uploadDate' => $this->faker->date,
            'originalAuthor' => $this->faker->name,
            'token' => $this->faker->md5,
            'userRole' => "1",
            'status' => "1",
            'owningUser' => "1",
            'sessionLearningMaterials' => [1],
            'courseLearningMaterials' => [1],
            'citation' => $this->faker->text,
            'type' => "citation"
        );

        $arr[] = array(
            'id' => 2,
            'title' => $this->faker->text(30),
            'description' => $this->faker->text,
            'uploadDate' => $this->faker->date,
            'originalAuthor' => $this->faker->name,
            'token' => $this->faker->md5,
            'userRole' => "2",
            'status' => "1",
            'owningUser' => "1",
            'courseLearningMaterials' => [2],
            'citation' => $this->faker->text,
            'type' => "citation"
        );


        return $arr;
    }

    public function create()
    {
        return [];
    }

    public function createInvalid()
    {
        return [];
    }
}
