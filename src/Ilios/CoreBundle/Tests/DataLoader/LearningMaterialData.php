<?php

namespace Ilios\CoreBundle\Tests\DataLoader;

class LearningMaterialData extends AbstractDataLoader
{
    /**
     * {@inheritdoc}
     */
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
            'citation' => $this->faker->text,
            'link' => '',
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
            'citation' => '',
            'link' => $this->faker->url,
        );

        return $arr;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return array(
            'id' => 3,
            'title' => $this->faker->text(30),
            'description' => $this->faker->text,
            'originalAuthor' => $this->faker->name,
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [],
            'userRole' => "2",
            'status' => "1",
            'owningUser' => "1",
            'citation' => $this->faker->text,
            'link' => '',
        );
    }

    /**
     * @return array
     */
    public function createCitation() {
        return array(
            'id' => 4,
            'title' => $this->faker->text(30),
            'description' => $this->faker->text,
            'originalAuthor' => $this->faker->name,
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [],
            'userRole' => "2",
            'status' => "1",
            'owningUser' => "1",
            'citation' => $this->faker->text,
            'link' => '',
        );
    }

    /**
     * @return array
     */
    public function createLink() {
        return array(
            'id' => 5,
            'title' => $this->faker->text(30),
            'description' => $this->faker->text,
            'originalAuthor' => $this->faker->name,
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [],
            'userRole' => "2",
            'status' => "1",
            'owningUser' => "1",
            'citation' => null,
            'link' => $this->faker->url,
        );
    }

    /**
     * @throws \Exception
     */
    public function createFile()
    {
        throw new \Exception('Not implemented yet');
    }

    /**
     * @return array
     */
    public function createInvalid()
    {
        return [];
    }

    /**
     * @return array
     */
    public function createInvalidCitation() {
        return array(
            'id' => 6,
            'title' => $this->faker->text(30),
            'description' => $this->faker->text,
            'originalAuthor' => $this->faker->name,
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [],
            'userRole' => "2",
            'status' => "1",
            'owningUser' => "1",
            'citation' => $this->faker->text(600), // too long
            'link' => '',
        );
    }

    /**
     * @return array
     */
    public function createInvalidLink() {
        return array(
            'id' => 7,
            'title' => $this->faker->text(30),
            'description' => $this->faker->text,
            'originalAuthor' => $this->faker->name,
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [],
            'userRole' => "2",
            'status' => "1",
            'owningUser' => "1",
            'citation' => '',
            'link' => 'this-is-not-an-url',
        );
    }

    public function createInvalidFile()
    {
        throw new \Exception('Not implemented yet');
    }
}
