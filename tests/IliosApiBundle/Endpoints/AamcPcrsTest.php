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

    protected $testName =  'aamcpcrs';

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
        $this->relatedPostDataTest($data, $postData, 'aamcPcrses', 'competencies');
    }

}