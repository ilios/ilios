<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Tests\DataLoader\ProgramData;
use App\Tests\Fixture\LoadCurriculumInventoryAcademicLevelData;
use App\Tests\Fixture\LoadCurriculumInventoryExportData;
use App\Tests\Fixture\LoadCurriculumInventoryInstitutionData;
use App\Tests\Fixture\LoadCurriculumInventoryReportData;
use App\Tests\Fixture\LoadCurriculumInventorySequenceBlockData;
use App\Tests\Fixture\LoadCurriculumInventorySequenceData;
use App\Tests\Fixture\LoadProgramData;
use DateTime;
use Symfony\Component\HttpFoundation\Response;
use App\Tests\ReadWriteEndpointTest;

/**
 * CurriculumInventoryReport API endpoint Test.
 * @group api_5
 */
class CurriculumInventoryReportTest extends ReadWriteEndpointTest
{
    protected string $testName =  'curriculumInventoryReports';

    protected function getFixtures(): array
    {
        return [
            LoadProgramData::class,
            LoadCurriculumInventoryReportData::class,
            LoadCurriculumInventoryExportData::class,
            LoadCurriculumInventorySequenceData::class,
            LoadCurriculumInventorySequenceBlockData::class,
            LoadCurriculumInventoryAcademicLevelData::class,
            LoadCurriculumInventoryInstitutionData::class,
        ];
    }

    /**
     * @inheritDoc
     */
    public static function putsToTest(): array
    {
        return [
            'name' => ['name', 'salt'],
            'nullName' => ['name', null],
            'description' => ['description', 'lorem ipsum'],
            'nullDescription' => ['description', null],
            'year' => ['year', 2022],
            'startDate' => ['startDate', '2012-03-01', $skipped = true],
            'endDate' => ['endDate', '2012-04-01', $skipped = true],
            'export' => ['export', 2, $skipped = true],
            'sequence' => ['sequence', 1, $skipped = true],
            'sequenceBlocks' => ['sequenceBlocks', [1], $skipped = true],
            'program' => ['program', 'too much salt', $skipped = true],
            'academicLevels' => ['academicLevels', [1], $skipped = true],
            'administrators' => ['administrators', [1]],
            'removeAdministrators' => ['administrators', []],

        ];
    }

    /**
     * @inheritDoc
     */
    public static function readOnlyPropertiesToTest(): array
    {
        return [
            'id' => ['id', 1, 99],
        ];
    }

    /**
     * @inheritDoc
     */
    public static function filtersToTest(): array
    {
        return [
            'id' => [[0], ['id' => 1]],
            'name' => [[1], ['name' => 'second report']],
            'description' => [[2], ['description' => 'third report']],
            'year' => [[1], ['year' => 2015]],
            'startDate' => [[0], ['startDate' => 'test'], $skipped = true],
            'endDate' => [[0], ['endDate' => 'test'], $skipped = true],
            'export' => [[0], ['export' => 1], $skipped = true],
            'sequence' => [[0], ['sequence' => 1], $skipped = true],
            'sequenceBlocks' => [[0], ['sequenceBlocks' => [1]], $skipped = true],
            'program' => [[0, 1, 2], ['program' => 1]],
            'academicLevels' => [[0], ['academicLevels' => [1]], $skipped = true],
        ];
    }

    public static function graphQLFiltersToTest(): array
    {
        $filters = self::filtersToTest();
        $filters['ids'] = [[1, 2], ['ids' => [2, 3]]];

        return $filters;
    }

    protected function compareData(array $expected, array $result)
    {
        unset($result['absoluteFileUri']);
        $this->assertEquals(
            $expected,
            $result
        );
    }

    protected function compareGraphQLData(array $expected, object $result): void
    {
        unset($result->absoluteFileUri);
        parent::compareGraphQLData($expected, $result);
    }

    protected function getOneTest(): mixed
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $loader = $this->getDataLoader();
        $data = $loader->getOne();
        $returnedData = $this->getOne($endpoint, $responseKey, $data['id']);
        $this->assertNotEmpty($returnedData['absoluteFileUri']);
        $this->compareData($data, $returnedData);

        return $returnedData;
    }

    protected function getAllTest(): mixed
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $loader = $this->getDataLoader();
        $data = $loader->getAll();
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_{$endpoint}_getall",
                ['version' => $this->apiVersion]
            ),
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );
        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_OK);
        $responses = json_decode($response->getContent(), true)[$responseKey];

        foreach ($responses as $i => $response) {
            $this->assertNotEmpty($response['absoluteFileUri']);
            unset($response['absoluteFileUri']);
            $this->compareData($data[$i], $response);
        }

        return $responses;
    }

    protected function postTest(array $data, array $postData): mixed
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $postKey = $this->getCamelCasedSingularName();
        $responseData = $this->postOne($endpoint, $postKey, $responseKey, $postData);
        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getOne($endpoint, $responseKey, $responseData['id']);

        $this->assertNotEmpty($fetchedResponseData['absoluteFileUri']);
        $this->assertEquals(
            10,
            count($fetchedResponseData['academicLevels']),
            'There should be 10 academic levels ids.'
        );
        $this->assertNotEmpty($fetchedResponseData['sequence'], 'A sequence id should be present.');

        unset($fetchedResponseData['sequence']);
        unset($fetchedResponseData['academicLevels']);
        // don't compare sequence and academic level ids.
        if (array_key_exists('sequence', $data)) {
            $fetchedResponseData['sequence'] = $data['sequence'];
        }
        if (array_key_exists('academicLevels', $data)) {
            $fetchedResponseData['academicLevels'] = $data['academicLevels'];
        }

        $this->compareData($data, $fetchedResponseData);

        return $fetchedResponseData;
    }

    /**
     * Test saving new data to the JSON:API
     */
    protected function postJsonApiTest(object $postData, array $data): mixed
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $responseData = $this->postOneJsonApi($postData);

        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getOne($endpoint, $responseKey, $responseData->id);

        $this->assertNotEmpty($fetchedResponseData['absoluteFileUri']);
        $this->assertEquals(
            10,
            count($fetchedResponseData['academicLevels']),
            'There should be 10 academic levels ids.'
        );
        $this->assertNotEmpty($fetchedResponseData['sequence'], 'A sequence id should be present.');

        unset($fetchedResponseData['sequence']);
        unset($fetchedResponseData['academicLevels']);
        // don't compare sequence and academic level ids.
        if (array_key_exists('sequence', $data)) {
            $fetchedResponseData['sequence'] = $data['sequence'];
        }
        if (array_key_exists('academicLevels', $data)) {
            $fetchedResponseData['academicLevels'] = $data['academicLevels'];
        }

        $this->compareData($data, $fetchedResponseData);

        return $fetchedResponseData;
    }

    protected function postManyTest(array $data): mixed
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $responseData = $this->postMany($endpoint, $responseKey, $data);
        $ids = array_map(fn(array $arr) => $arr['id'], $responseData);
        $filters = [
            'filters[id]' => $ids,
            'limit' => count($ids)
        ];
        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getFiltered($endpoint, $responseKey, $filters);

        usort($fetchedResponseData, fn($a, $b) => $a['id'] <=> $b['id']);

        foreach ($data as $i => $datum) {
            $response = $fetchedResponseData[$i];

            $this->assertNotEmpty($response['absoluteFileUri']);
            $this->assertEquals(10, count($response['academicLevels']), 'There should be 10 academic levels ids.');
            $this->assertNotEmpty($response['sequence'], 'A sequence id should be present.');
            unset($response['sequence']);
            unset($response['academicLevels']);
            // don't compare sequence and academic level ids.
            if (array_key_exists('sequence', $datum)) {
                $response['sequence'] = $datum['sequence'];
            }
            if (array_key_exists('academicLevels', $datum)) {
                $response['academicLevels'] = $datum['academicLevels'];
            }

            $this->compareData($datum, $response);
        }

        return $fetchedResponseData;
    }

    protected function postManyJsonApiTest(object $postData, array $data): mixed
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $responseData = $this->postManyJsonApi($postData);
        $ids = array_column($responseData, 'id');
        $filters = [
            'filters[id]' => $ids
        ];
        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getFiltered($endpoint, $responseKey, $filters);

        usort($fetchedResponseData, fn($a, $b) => $a['id'] <=> $b['id']);

        foreach ($data as $i => $datum) {
            $response = $fetchedResponseData[$i];

            $this->assertNotEmpty($response['absoluteFileUri']);
            $this->assertEquals(10, count($response['academicLevels']), 'There should be 10 academic levels ids.');
            $this->assertNotEmpty($response['sequence'], 'A sequence id should be present.');
            unset($response['sequence']);
            unset($response['academicLevels']);
            // don't compare sequence and academic level ids.
            if (array_key_exists('sequence', $datum)) {
                $response['sequence'] = $datum['sequence'];
            }
            if (array_key_exists('academicLevels', $datum)) {
                $response['academicLevels'] = $datum['academicLevels'];
            }

            $this->compareData($datum, $response);
        }

        return $fetchedResponseData;
    }

    protected function putTest(array $data, array $postData, $id, $new = false): mixed
    {
        $endpoint = $this->getPluralName();
        $putResponseKey = $this->getCamelCasedSingularName();
        $getResponseKey = $this->getCamelCasedPluralName();
        $responseData = $this->putOne($endpoint, $putResponseKey, $id, $postData, $new);
        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getOne($endpoint, $getResponseKey, $responseData['id']);

        $this->assertNotEmpty($fetchedResponseData['absoluteFileUri']);

        $this->compareData($data, $fetchedResponseData);

        return $fetchedResponseData;
    }



    public function testPutForAllData()
    {
        $putsToTest = $this->putsToTest();

        $firstPut = array_shift($putsToTest);
        $changeKey = $firstPut[0];
        $changeValue = $firstPut[1];
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $nonExportedReports = array_filter($all, fn($report) => !array_key_exists('export', $report));
        foreach ($nonExportedReports as $data) {
            $data[$changeKey] = $changeValue;

            $this->putTest($data, $data, $data['id']);
        }
    }

    public function testPatchForAllDataJsonApi()
    {
        $putsToTest = $this->putsToTest();

        $firstPut = array_shift($putsToTest);
        $changeKey = $firstPut[0];
        $changeValue = $firstPut[1];
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $nonExportedReports = array_filter($all, fn($report) => !array_key_exists('export', $report));
        foreach ($nonExportedReports as $data) {
            $data[$changeKey] = $changeValue;
            $jsonApiData = $dataLoader->createJsonApi($data);
            $this->patchJsonApiTest($data, $jsonApiData);
        }
    }

    public function testRolloverCurriculumInventoryReport()
    {
        $dataLoader = $this->getDataLoader();
        $report = $dataLoader->getOne();

        $this->createJsonRequest(
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_curriculuminventoryreports_rollover',
                [
                    'version' => $this->apiVersion,
                    'id' => $report['id'],
                ]
            ),
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_CREATED);
        $data = json_decode($response->getContent(), true)['curriculumInventoryReports'];
        $newReport = $data[0];

        // compare reports
        $this->assertEquals($report['name'], $newReport['name']);
        $this->assertEquals($report['description'], $newReport['description']);
        $this->assertEquals($report['year'], $newReport['year']);
        $this->assertEquals($report['program'], $newReport['program']);
        $this->assertEquals('07/01/' . $report['year'], date_create($newReport['startDate'])->format('m/d/Y'));
        $this->assertEquals('06/30/' . ($report['year'] + 1), date_create($newReport['endDate'])->format('m/d/Y'));
        $this->assertArrayNotHasKey('export', $newReport);
        $this->assertNotEmpty($newReport['absoluteFileUri']);

        $newSequence = $this->getOne(
            'curriculuminventorysequences',
            'curriculumInventorySequences',
            $newReport['sequence']
        );
        $sequence = $this->getOne(
            'curriculuminventorysequences',
            'curriculumInventorySequences',
            $report['sequence']
        );

        $this->assertSame($sequence['description'], $newSequence['description']);

        // map and compare academic levels
        $levels = $this->getFiltered(
            'curriculuminventoryacademiclevels',
            'curriculumInventoryAcademicLevels',
            ['filters[report]' => $report['id']]
        );
        $levelsById = [];
        $levelsByLevel = [];
        foreach ($levels as $level) {
            $levelsById[$level['id']] = $level;
            $levelsByLevel[$level['level']] = $level; // "Bleed with +1 bleed."
        }

        $newLevels = $this->getFiltered(
            'curriculuminventoryacademiclevels',
            'curriculumInventoryAcademicLevels',
            ['filters[report]' => $newReport['id']]
        );
        $newLevelsById = [];
        foreach ($newLevels as $level) {
            $newLevelsById[$level['id']] = $level;
            $originalLevel = $levelsByLevel[$level['level']];
            $this->assertSame($originalLevel['name'], $level['name']);
            $this->assertSame($originalLevel['description'], $level['description']);
            $this->assertSame(count($originalLevel['startingSequenceBlocks']), count($level['startingSequenceBlocks']));
            $this->assertSame(count($originalLevel['endingSequenceBlocks']), count($level['endingSequenceBlocks']));
        }
        $this->assertSame(count($levels), count($newLevels));

        // compare sequence blocks.
        $blocks = $this->getFiltered(
            'curriculuminventorysequenceblocks',
            'curriculumInventorySequenceBlocks',
            ['filters[report]' => $report['id']]
        );
        $blocksById = [];
        $blocksByTitle = [];
        foreach ($blocks as $block) {
            $blocksById[$block['id']] = $block;
            $blocksByTitle[$block['title']] = $block; // assuming that fixture seq blocks have unique titles.
        }

        $newBlocks = $this->getFiltered(
            'curriculuminventorysequenceblocks',
            'curriculumInventorySequenceBlocks',
            ['filters[report]' => $newReport['id']]
        );
        $newBlocksById = [];
        foreach ($newBlocks as $block) {
            $newBlocksById[$block['id']] = $block;
        }

        $this->assertSame(count($blocks), count($newBlocks));
        foreach ($newBlocks as $newBlock) {
            $block = $blocksByTitle[$newBlock['title']];
            $this->assertSame($block['required'], $newBlock['required']);
            $this->assertSame($block['childSequenceOrder'], $newBlock['childSequenceOrder']);
            $this->assertSame($block['orderInSequence'], $newBlock['orderInSequence']);
            $this->assertSame($block['minimum'], $newBlock['minimum']);
            $this->assertSame($block['maximum'], $newBlock['maximum']);
            $this->assertSame($block['track'], $newBlock['track']);
            $this->assertSame($block['startDate'], $newBlock['startDate']);
            $this->assertSame($block['endDate'], $newBlock['endDate']);
            $this->assertSame($block['duration'], $newBlock['duration']);
            $this->assertSame(
                $levelsById[$block['startingAcademicLevel']]['level'],
                $newLevelsById[$newBlock['startingAcademicLevel']]['level']
            );
            $this->assertSame(
                $levelsById[$block['endingAcademicLevel']]['level'],
                $newLevelsById[$newBlock['endingAcademicLevel']]['level']
            );
            $this->assertFalse(array_key_exists('course', $newBlock));
            $this->assertEmpty($newBlock['sessions']);
            $this->assertSame(count($block['children']), count($newBlock['children']));
            if (count($newBlock['children'])) {
                foreach ($newBlock['children'] as $childId) {
                    $newChild = $newBlocksById[$childId];
                    $oldChild = $blocksByTitle[$newChild['title']];
                    $this->assertSame((int) $block['id'], (int) $oldChild['parent']);
                }
            }
            if (array_key_exists('parent', $newBlock)) {
                $newParent = $newBlocksById[$newBlock['parent']];
                $oldParent = $blocksById[$block['parent']];
                $this->assertSame($oldParent['title'], $newParent['title']);
            } else {
                $this->assertFalse(array_key_exists('parent', $block));
            }
        }
    }

    public function testRolloverCurriculumInventoryReportNotFound()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_curriculuminventoryreports_rollover',
                [
                    'version' => $this->apiVersion,
                    'id' => '100',
                ]
            ),
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function testRolloverCurriculumInventoryReportWithOverrides()
    {
        $dataLoader = $this->getDataLoader();
        $report = $dataLoader->getOne();

        $postData = self::getContainer()->get(ProgramData::class)->create();
        unset($postData['id']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl($this->kernelBrowser, 'app_api_programs_post', [
                'version' => $this->apiVersion
            ]),
            json_encode(['programs' => [$postData]]),
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );
        $newProgramId = json_decode($this->kernelBrowser->getResponse()->getContent(), true)['programs'][0]['id'];


        $overrides = [
            'name' => strrev($report['name']),
            'description' => strrev($report['description']),
            'year' => $report['year'] + 1,
            'program' => $newProgramId,
        ];
        $parameters = array_merge($overrides, [
            'version' => $this->apiVersion,
            'object' => 'curriculuminventoryreports',
            'id' => $report['id'],
        ]);
        $this->createJsonRequest(
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_curriculuminventoryreports_rollover',
                $parameters
            ),
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_CREATED);
        $data = json_decode($response->getContent(), true)['curriculumInventoryReports'];
        $newReport = $data[0];

        $this->assertSame($overrides['name'], $newReport['name']);
        $this->assertNotSame($report['name'], $newReport['name']);
        $this->assertSame($overrides['description'], $newReport['description']);
        $this->assertNotSame($report['description'], $newReport['description']);
        $this->assertSame((int) $overrides['year'], (int) $newReport['year']);
        $this->assertNotSame((int) $report['year'], (int) $newReport['year']);
        $this->assertSame($overrides['program'], $newReport['program']);
        $this->assertNotSame($report['program'], $newReport['program']);
    }


    public function testRolloverExportedCurriculumInventoryReport()
    {
        $dataLoader = $this->getDataLoader();
        $reports = $dataLoader->getAll();
        $report = $reports[1];
        $this->assertArrayHasKey('export', $report);

        $this->createJsonRequest(
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_curriculuminventoryreports_rollover',
                [
                    'version' => $this->apiVersion,
                    'id' => $report['id'],
                ]
            ),
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_CREATED);
        $data = json_decode($response->getContent(), true)['curriculumInventoryReports'];
        $newReport = $data[0];

        // compare reports
        $this->assertSame($report['name'], $newReport['name']);
        $this->assertSame($report['description'], $newReport['description']);
    }

    public function testGetVerificationPreview()
    {
        $dataLoader = $this->getDataLoader();
        $reports = $dataLoader->getAll();
        $report = $reports[1];

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_curriculuminventoryreports_verificationpreview',
                [
                    'version' => $this->apiVersion,
                    'id' => $report['id'],
                ]
            ),
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_OK);
        $data = json_decode($response->getContent(), true)['preview'];
        $this->assertCount(9, $data);
        $this->assertArrayHasKey('program_expectations_mapped_to_pcrs', $data);
        $this->assertArrayHasKey('primary_instructional_methods_by_non_clerkship_sequence_blocks', $data);
        $this->assertArrayHasKey('non_clerkship_sequence_block_instructional_time', $data);
        $this->assertArrayHasKey('clerkship_sequence_block_instructional_time', $data);
        $this->assertArrayHasKey('instructional_method_counts', $data);
        $this->assertArrayHasKey('non_clerkship_sequence_block_assessment_methods', $data);
        $this->assertArrayHasKey('clerkship_sequence_block_assessment_methods', $data);
        $this->assertArrayHasKey('all_events_with_assessments_tagged_as_formative_or_summative', $data);
        $this->assertArrayHasKey('all_resource_types', $data);
    }

    public function testGetVerificationPreviewNotFound()
    {
        $invalidReportId = 1000;
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                'app_api_curriculuminventoryreports_verificationpreview',
                [
                    'version' => $this->apiVersion,
                    'id' => $invalidReportId,
                ]
            ),
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_NOT_FOUND);
    }
}
