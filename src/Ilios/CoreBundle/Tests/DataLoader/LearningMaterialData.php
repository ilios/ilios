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
            'title' => 'firstlm' . $this->faker->text(30),
            'description' => 'desc1' . $this->faker->text,
            'originalAuthor' => $this->faker->name,
            'userRole' => "1",
            'status' => "1",
            'owningUser' => "1",
            'copyrightRationale' => $this->faker->text,
            'copyrightPermission' => true,
            'sessionLearningMaterials' => [1],
            'courseLearningMaterials' => ['1', '3'],
            'citation' => $this->faker->text,
        );

        $arr[] = array(
            'id' => 2,
            'title' => 'secondlm' . $this->faker->text(30),
            'description' => 'desc2' . $this->faker->text,
            'originalAuthor' => $this->faker->name,
            'userRole' => "2",
            'status' => "1",
            'owningUser' => "1",
            'copyrightRationale' => $this->faker->text,
            'copyrightPermission' => true,
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [2],
            'link' => $this->faker->url,
        );

        $arr[] = array(
            'id' => 3,
            'title' => 'thirdlm' . $this->faker->text(30),
            'description' => 'desc3' . $this->faker->text,
            'originalAuthor' => $this->faker->name,
            'userRole' => "2",
            'status' => "1",
            'owningUser' => "1",
            'copyrightRationale' => $this->faker->text,
            'copyrightPermission' => true,
            'sessionLearningMaterials' => ['2'],
            'courseLearningMaterials' => [],
            'filename' => 'testfile.txt',
            'mimetype' => 'text/plain',
            'filesize' => 1000
        );

        return $arr;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        return array(
            'title' => $this->faker->text(30),
            'description' => $this->faker->text,
            'originalAuthor' => $this->faker->name,
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [],
            'userRole' => "2",
            'status' => "1",
            'owningUser' => "1",
            'copyrightRationale' => $this->faker->text,
            'copyrightPermission' => true,
            'citation' => $this->faker->text,
        );
    }

    /**
     * @return array
     */
    public function createCitation()
    {
        return array(
            'title' => $this->faker->text(30),
            'description' => $this->faker->text,
            'originalAuthor' => $this->faker->name,
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [],
            'userRole' => "2",
            'status' => "1",
            'owningUser' => "1",
            'citation' => $this->faker->text,
        );
    }

    /**
     * @return array
     */
    public function createLink()
    {
        return array(
            'title' => $this->faker->text(30),
            'description' => $this->faker->text,
            'originalAuthor' => $this->faker->name,
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [],
            'userRole' => "2",
            'status' => "1",
            'owningUser' => "1",
            'link' => $this->faker->url,
        );
    }

    /**
     * @throws \Exception
     */
    public function createFile()
    {
        return array(
            'title' => $this->faker->text(30),
            'description' => $this->faker->text,
            'originalAuthor' => $this->faker->name,
            'userRole' => "2",
            'status' => "1",
            'owningUser' => "1",
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [],
            'copyrightRationale' => $this->faker->text,
            'copyrightPermission' => true,
        );
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
    public function createInvalidCitation()
    {
        return array(
            'title' => $this->faker->text(30),
            'description' => $this->faker->text,
            'originalAuthor' => $this->faker->name,
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [],
            'userRole' => "2",
            'status' => "1",
            'owningUser' => "1",
            'citation' => $this->faker->text(600), // too long
        );
    }

    /**
     * @return array
     */
    public function createInvalidLink()
    {
        return array(
            'title' => $this->faker->text(30),
            'description' => $this->faker->text,
            'originalAuthor' => $this->faker->name,
            'sessionLearningMaterials' => [],
            'courseLearningMaterials' => [],
            'userRole' => "2",
            'status' => "1",
            'owningUser' => "1",
            'link' => 'this-is-not-an-url',
        );
    }

    public function createInvalidFile()
    {
        throw new \Exception('Not implemented yet');
    }
}
