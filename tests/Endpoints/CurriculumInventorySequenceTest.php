<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use PHPUnit\Framework\Attributes\Group;
use App\Tests\DataLoader\CurriculumInventoryReportData;
use App\Tests\Fixture\LoadCurriculumInventoryReportData;
use App\Tests\Fixture\LoadCurriculumInventorySequenceData;

/**
 * CurriculumInventorySequence API endpoint Test.
 */
#[Group('api_1')]
class CurriculumInventorySequenceTest extends AbstractReadWriteEndpoint
{
    protected string $testName =  'curriculumInventorySequences';

    protected function getFixtures(): array
    {
        return [
            LoadCurriculumInventorySequenceData::class,
            LoadCurriculumInventoryReportData::class,
        ];
    }

    public static function putsToTest(): array
    {
        return [
            'description' => ['description', 'some text here'],
        ];
    }

    public static function readOnlyPropertiesToTest(): array
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    public static function filtersToTest(): array
    {
        return [
            'id' => [[0], ['id' => 1]],
            'ids' => [[0, 1], ['id' => [1, 2]]],
            'report' => [[1], ['report' => 2]],
            'description' => [[1], ['description' => 'second description']],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        $filters = self::filtersToTest();
        $filters['ids'] = [[0, 1], ['ids' => [1, 2]]];

        return $filters;
    }

    /**
     * We need to create additional reports to go with each Sequence
     * however when new reports are created a sequence is automatically created
     * for them.  So we need to delete each of the new fresh sequences, so we can create
     * new ones of our own and link them to the report.
     */
    protected function createMany(int $count, string $jwt): array
    {
        $reportDataLoader = self::getContainer()->get(CurriculumInventoryReportData::class);
        $reports = $reportDataLoader->createMany($count);
        $savedReports = $this->postMany('curriculuminventoryreports', 'curriculumInventoryReports', $reports, $jwt);


        $dataLoader = $this->getDataLoader();
        $data = [];

        foreach ($savedReports as $i => $report) {
            $sequenceId = $report['sequence'];
            $this->deleteOne('curriculuminventorysequences', $sequenceId, $jwt);
            $arr = $dataLoader->create();
            $arr['id'] += ($i + $count);
            $arr['report'] = $report['id'];

            $data[] = $arr;
        }

        return $data;
    }

    protected function runPostManyTest(string $jwt): void
    {
        $data = $this->createMany(4, $jwt);
        $this->postManyTest($data, $jwt);
    }

    protected function runPostManyJsonApiTest(string $jwt): void
    {
        $data = $this->createMany(4, $jwt);
        $jsonApiData = $this->getDataLoader()->createBulkJsonApi($data);
        $this->postManyJsonApiTest($jsonApiData, $data, $jwt);
    }
}
