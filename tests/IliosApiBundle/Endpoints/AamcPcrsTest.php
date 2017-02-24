<?php

namespace Tests\IliosApiBundle\Endpoints;

use Doctrine\Common\Inflector\Inflector;
use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * AamcPcrs API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_1
 */
class AamcPcrsTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'aamcPcrs';

    public function setUp()
    {
        parent::setUp();
        Inflector::rules('singular', array(
            'uninflected' => array('aamcpcrs'),
        ));
    }

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadAamcPcrsData',
            'Tests\CoreBundle\Fixture\LoadCompetencyData'
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'description' => ['description', $this->getFaker()->text],
            'competencies' => ['competencies', [3]],
            'id' => ['id', 'new-id', $skipped = true],
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
            'id' => [[0], ['id' => 'aamc-pcrs-comp-c0101']],
            'description' => [[1], ['description' => 'second description']],
            'competencies' => [[0], ['competencies' => [1]]],
        ];
    }

    public function testPostTermAamcResourceType()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'aamcPcrses', 'competencies');
    }
}
