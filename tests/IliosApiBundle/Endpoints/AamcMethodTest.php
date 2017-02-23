<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * AamcMethod API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class AamcMethodTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'aamcMethods';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadAamcMethodData',
            'Tests\CoreBundle\Fixture\LoadSessionTypeData',
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'description' => ['description', $this->getFaker()->text],
            'sessionTypes' => ['sessionTypes', [1]],
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
            'id' => [[0], ['id' => 'AM001']],
            'description' => [[1], ['description' => 'filterable description']],
            'sessionTypes' => [[0], ['sessionTypes' => [1]]],
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
}
