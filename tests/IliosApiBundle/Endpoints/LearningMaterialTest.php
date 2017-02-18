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
    protected $testName =  'learningmaterials';

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
     */
    public function filtersToTest()
    {
        return [
            'id' => [[0], ['id' => 1]],
            'title' => [[0], ['title' => 'test']],
            'description' => [[0], ['description' => 'test']],
            'uploadDate' => [[0], ['uploadDate' => 'test']],
            'originalAuthor' => [[0], ['originalAuthor' => 'test']],
            'userRole' => [[0], ['userRole' => 1]],
            'status' => [[0], ['status' => 1]],
            'owningUser' => [[0], ['owningUser' => 1]],
            'sessionLearningMaterials' => [[0], ['sessionLearningMaterials' => [1]]],
            'courseLearningMaterials' => [[0], ['courseLearningMaterials' => [1]]],
            'citation' => [[0], ['citation' => 'test']],
            'copyrightPermission' => [[0], ['copyrightPermission' => false]],
            'copyrightRationale' => [[0], ['copyrightRationale' => 'test']],
            'filename' => [[0], ['filename' => 'test']],
            'mimetype' => [[0], ['mimetype' => 'test']],
            'filesize' => [[0], ['filesize' => 1]],
            'link' => [[0], ['link' => 'test']],
        ];
    }

}