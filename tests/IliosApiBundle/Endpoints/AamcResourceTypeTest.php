<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;

/**
 * AamcResourceType API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class AamcResourceTypeTest extends AbstractEndpointTest
{
    protected $testName =  'aamcresourcetypes';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadAamcResourceTypeData',
            'Tests\CoreBundle\Fixture\LoadTermData'
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
            'terms' => ['terms', [3]],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnliesToTest()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [
            'id' => [[2], ['id' => 'RE003']],
            'title' => [[0], ['title' => 'first title']],
            'description' => [[1], ['description' => 'second description']],
            'terms' => [[0], ['terms' => [1]]],
        ];
    }

    public function testPutId()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $id = $data['id'];
        $data['id'] = $this->getFaker()->text(10);

        $postData = $data;
        $this->putTest($data, $postData, $id);
    }

    public function testPostTermAamcResourceType()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'aamcResourceType', 'terms');
    }

}