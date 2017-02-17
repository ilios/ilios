<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * LearningMaterial API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class LearningMaterialTest extends AbstractEndpointTest
{
    protected $testName =  'learningmaterial';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadLearningMaterialData',
        ];
    }

    /**
     * @inheritDoc
     *
     * returns an array of field / value pairs to modify
     * the key for each item is reflected in the failure message
     * each one will be separately tested in a PUT request
     */
    public function putsToTest()
    {
        return [
            'title' => ['title', $this->getFaker()->text],
            'description' => ['description', $this->getFaker()->text],
            'originalAuthor' => ['originalAuthor', $this->getFaker()->text],
            'userRole' => ['userRole', 1],
            'status' => ['status', 1],
            'owningUser' => ['owningUser', 1],
            'sessionLearningMaterials' => ['sessionLearningMaterials', [1]],
            'courseLearningMaterials' => ['courseLearningMaterials', [1]],
            'citation' => ['citation', $this->getFaker()->text],
            'copyrightPermission' => ['copyrightPermission', false],
            'copyrightRationale' => ['copyrightRationale', $this->getFaker()->text],
            'filename' => ['filename', $this->getFaker()->text],
            'mimetype' => ['mimetype', $this->getFaker()->text],
            'filesize' => ['filesize', $this->getFaker()->randomDigit],
            'link' => ['link', $this->getFaker()->text],
        ];
    }

    /**
     * @inheritDoc
     *
     * returns an array of field / value pairs that are readOnly
     * the key for each item is reflected in the failure message
     * each one will be separately tested in a PUT request
     */
    public function readOnliesToTest()
    {
        return [
            'id' => ['id', 1, 99],
            'uploadDate' => ['uploadDate', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     *
     * returns an array of filters to test
     * the key for each item is reflected in the failure message
     * the first item is an array of the positions the expected items
     * can be found in the data loader
     * the second item is the filter we are testing
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['filters[id]' => 1]],
            'title' => [[0], ['filters[title]' => 'test']],
            'description' => [[0], ['filters[description]' => 'test']],
            'uploadDate' => [[0], ['filters[uploadDate]' => 'test']],
            'originalAuthor' => [[0], ['filters[originalAuthor]' => 'test']],
            'userRole' => [[0], ['filters[userRole]' => 1]],
            'status' => [[0], ['filters[status]' => 1]],
            'owningUser' => [[0], ['filters[owningUser]' => 1]],
            'sessionLearningMaterials' => [[0], ['filters[sessionLearningMaterials]' => [1]]],
            'courseLearningMaterials' => [[0], ['filters[courseLearningMaterials]' => [1]]],
            'citation' => [[0], ['filters[citation]' => 'test']],
            'copyrightPermission' => [[0], ['filters[copyrightPermission]' => false]],
            'copyrightRationale' => [[0], ['filters[copyrightRationale]' => 'test']],
            'filename' => [[0], ['filters[filename]' => 'test']],
            'mimetype' => [[0], ['filters[mimetype]' => 'test']],
            'filesize' => [[0], ['filters[filesize]' => 1]],
            'link' => [[0], ['filters[link]' => 'test']],
        ];
    }

}