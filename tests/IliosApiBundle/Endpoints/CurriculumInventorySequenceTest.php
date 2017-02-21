<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * CurriculumInventorySequence API endpoint Test.
 * @package Tests\IliosApiBundle\Endpoints
 * @group api_2
 */
class CurriculumInventorySequenceTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'curriculuminventorysequences';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadCurriculumInventorySequenceData',
            'Tests\CoreBundle\Fixture\LoadCurriculumInventoryReportData'
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'description' => ['description', $this->getFaker()->text],
        ];
    }

    /**
     * @inheritDoc
     */
    public function readOnliesToTest()
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
            'id' => [[0], ['id' => 1]],
            'ids' => [[0, 1], ['id' => [1, 2]]],
            'report' => [[1], ['report' => 2]],
            'description' => [[1], ['description' => 'second description']],
        ];
    }


    /**
     * We need to create additional reports to
     * go with each Sequence
     * @inheritdoc
     */
    public function testPostMany()
    {
        $count = 26;
        $reportDataLoader = $this->container->get('ilioscore.dataloader.curriculuminventoryreport');
        $reports = $reportDataLoader->createMany($count);
        $savedReports = $this->postMany('curriculuminventoryreports', $reports);

        $dataLoader = $this->getDataLoader();
        $data = [];

        foreach ($savedReports as $i => $report) {
            $arr = $dataLoader->create();
            $arr['id'] += $i;
            $arr['report'] = $report['id'];

            $data[] = $arr;
        }

        $this->postManyTest($data);
    }

}