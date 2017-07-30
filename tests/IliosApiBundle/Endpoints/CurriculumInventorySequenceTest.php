<?php

namespace Tests\IliosApiBundle\Endpoints;

use Tests\CoreBundle\DataLoader\CurriculumInventoryReportData;
use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * CurriculumInventorySequence API endpoint Test.
 * @group api_1
 */
class CurriculumInventorySequenceTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'curriculumInventorySequences';

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
            'id' => [[0], ['id' => 1]],
            'ids' => [[0, 1], ['id' => [1, 2]]],
            'report' => [[1], ['report' => 2]],
            'description' => [[1], ['description' => 'second description']],
        ];
    }


    /**
     * We need to create additional reports to go with each Sequence
     * however when new reports are created a sequence is automatically created
     * for them.  So we need to delete each of the new fresh sequences so we can create
     * new ones of our own and link them to the report.
     * @inheritdoc
     */
    public function testPostMany()
    {
        $count = 4;
        $reportDataLoader = $this->container->get(CurriculumInventoryReportData::class);
        $reports = $reportDataLoader->createMany($count);
        $savedReports = $this->postMany('curriculuminventoryreports', 'curriculumInventoryReports', $reports);


        $dataLoader = $this->getDataLoader();
        $data = [];

        foreach ($savedReports as $i => $report) {
            $sequenceId = $report['sequence'];
            $this->deleteOne('curriculuminventorysequences', $sequenceId);
            $arr = $dataLoader->create();
            $arr['id'] += $i;
            $arr['report'] = $report['id'];

            $data[] = $arr;
        }

        $this->postManyTest($data);
    }
}
