<?php

namespace Tests\IliosApiBundle\Endpoints;

use Symfony\Component\HttpFoundation\Response;
use Tests\IliosApiBundle\AbstractEndpointTest;
use Tests\IliosApiBundle\EndpointTestsTrait;

/**
 * CurriculumInventoryReport API endpoint Test.
 * @group api_5
 */
class CurriculumInventoryReportTest extends AbstractEndpointTest
{
    use EndpointTestsTrait;

    protected $testName =  'curriculumInventoryReports';

    /**
     * @inheritdoc
     */
    protected function getFixtures()
    {
        return [
            'Tests\CoreBundle\Fixture\LoadProgramData',
            'Tests\CoreBundle\Fixture\LoadCurriculumInventoryReportData',
            'Tests\CoreBundle\Fixture\LoadCurriculumInventoryExportData',
            'Tests\CoreBundle\Fixture\LoadCurriculumInventorySequenceData',
            'Tests\CoreBundle\Fixture\LoadCurriculumInventorySequenceBlockData',
            'Tests\CoreBundle\Fixture\LoadCurriculumInventoryAcademicLevelData'
        ];
    }

    /**
     * @inheritDoc
     */
    public function putsToTest()
    {
        return [
            'name' => ['name', $this->getFaker()->text],
            'description' => ['description', $this->getFaker()->text],
            'year' => ['year', $this->getFaker()->randomDigit],
            'startDate' => ['startDate', $this->getFaker()->iso8601, $skipped = true],
            'endDate' => ['endDate', $this->getFaker()->iso8601, $skipped = true],
            'export' => ['export', 2, $skipped = true],
            'sequence' => ['sequence', 1, $skipped = true],
            'sequenceBlocks' => ['sequenceBlocks', [1], $skipped = true],
            'program' => ['program', $this->getFaker()->text, $skipped = true],
            'academicLevels' => ['academicLevels', [1], $skipped = true],
            'administrators' => ['administrators', [1]],

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

    protected function compareData(array $expected, array $result)
    {
        unset($result['absoluteFileUri']);
        $this->assertEquals(
            $expected,
            $result
        );
    }

    protected function getOneTest()
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

    protected function getAllTest()
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $loader = $this->getDataLoader();
        $data = $loader->getAll();
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'ilios_api_getall',
                ['version' => 'v1', 'object' => $endpoint]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_OK);
        $responses = json_decode($response->getContent(), true)[$responseKey];

        foreach ($responses as $i => $response) {
            $this->assertNotEmpty($response['absoluteFileUri']);
            unset($response['absoluteFileUri']);
            $this->compareData($data[$i], $response);
        }

        return $responses;
    }

    protected function postTest(array $data, array $postData)
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

    protected function postManyTest(array $data)
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $responseData = $this->postMany($endpoint, $responseKey, $data);
        $ids = array_map(function (array $arr) {
            return $arr['id'];
        }, $responseData);
        $filters = [
            'filters[id]' => $ids,
            'limit' => count($ids)
        ];
        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getFiltered($endpoint, $responseKey, $filters);

        usort($fetchedResponseData, function ($a, $b) {
            return strnatcasecmp($a['id'], $b['id']);
        });

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

    protected function putTest(array $data, array $postData, $id, $new = false)
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
        $nonExportedReports = array_filter($all, function ($report) {
            return !array_key_exists('export', $report);
        });
        foreach ($nonExportedReports as $data) {
            $data[$changeKey] = $changeValue;

            $this->putTest($data, $data, $data['id']);
        }
    }

    public function testRolloverCurriculumInventoryReport()
    {
        $dataLoader = $this->getDataLoader();
        $report = $dataLoader->getOne();

        $this->createJsonRequest(
            'POST',
            $this->getUrl(
                'ilios_api_curriculuminventoryreport_rollover',
                [
                    'version' => 'v1',
                    'object' => 'curriculuminventoryreports',
                    'id' => $report['id'],
                ]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_CREATED);
        $data = json_decode($response->getContent(), true)['curriculumInventoryReports'];
        $newReport = $data[0];

        // compare reports
        $this->assertSame($report['name'], $newReport['name']);
        $this->assertSame($report['description'], $newReport['description']);
        $this->assertSame($report['year'], $newReport['year']);
        $this->assertSame($report['program'], $newReport['program']);
        $this->assertSame($report['startDate'], $newReport['startDate']);
        $this->assertSame($report['endDate'], $newReport['endDate']);
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
            $this->assertSame(count($originalLevel['sequenceBlocks']), count($level['sequenceBlocks']));
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
                $levelsById[$block['academicLevel']]['level'],
                $newLevelsById[$newBlock['academicLevel']]['level']
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
                'ilios_api_curriculuminventoryreport_rollover',
                [
                    'version' => 'v1',
                    'object' => 'curriculuminventoryreports',
                    'id' => '-1',
                ]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function testRolloverCurriculumInventoryReportWithOverrides()
    {
        $dataLoader = $this->getDataLoader();
        $report = $dataLoader->getOne();
        $overrides = [
            'name' => strrev($report['name']),
            'description' => strrev($report['description']),
            'year' => $report['year'] + 1,
        ];
        $parameters = array_merge($overrides, [
            'version' => 'v1',
            'object' => 'curriculuminventoryreports',
            'id' => $report['id'],
        ]);
        $this->createJsonRequest(
            'POST',
            $this->getUrl(
                'ilios_api_curriculuminventoryreport_rollover',
                $parameters
            ),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_CREATED);
        $data = json_decode($response->getContent(), true)['curriculumInventoryReports'];
        $newReport = $data[0];

        $this->assertSame($overrides['name'], $newReport['name']);
        $this->assertNotSame($report['name'], $newReport['name']);
        $this->assertSame($overrides['description'], $newReport['description']);
        $this->assertNotSame($report['description'], $newReport['description']);
        $this->assertSame((int) $overrides['year'], (int) $newReport['year']);
        $this->assertNotSame((int) $report['year'], (int) $newReport['year']);
    }
}
