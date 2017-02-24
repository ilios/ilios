<?php

namespace Tests\IliosApiBundle\Endpoints;

use Ilios\CoreBundle\Entity\AlertChangeTypeInterface;
use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * AlertChangeType API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class AlertChangeTypeTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'alertChangeTypes';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadAlertChangeTypeData',
            'Tests\CoreBundle\Fixture\LoadAlertData'
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'title' => ['title', $this->getFaker()->text(30)],
            'alerts' => ['alerts', [1]],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnlyPropertiesToTest()
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     */
    public function filtersToTest()
    {
        return [
            'id' => [[2], ['id' => AlertChangeTypeInterface::CHANGE_TYPE_LEARNING_MATERIAL]],
            'ids' => [[0, 1, 3], ['id' => [
                AlertChangeTypeInterface::CHANGE_TYPE_TIME,
                AlertChangeTypeInterface::CHANGE_TYPE_LOCATION,
                AlertChangeTypeInterface::CHANGE_TYPE_INSTRUCTOR
            ]]],
            'title' => [[1], ['title' => 'second title']],
            'alerts' => [[0], ['alerts' => [1]]],
        ];
    }

    public function testPostAlertAlertChangeType()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->relatedPostDataTest($data, $postData, 'changeTypes', 'alerts');
    }
}
