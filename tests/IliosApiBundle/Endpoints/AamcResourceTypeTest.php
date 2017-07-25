<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * AamcResourceType API endpoint Test.
 * @group api_3
 */
class AamcResourceTypeTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'aamcResourceTypes';

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
            'id' => ['id', 'FK1', $skipped = true],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnlyPropertiesToTest()
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

    public function testPostTermAamcResourceType()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'aamcResourceType', 'terms');
    }

    public function testPutResourceTypeWithExtraData()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $data['garbage'] = 'LA Dodgers';

        $this->badPostTest($data);
    }
}
