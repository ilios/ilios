<?php

namespace Tests\CoreBundle\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * CurriculumInventoryReport controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class CurriculumInventoryReportControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Tests\CoreBundle\Fixture\LoadProgramData',
            'Tests\CoreBundle\Fixture\LoadCurriculumInventoryReportData',
            'Tests\CoreBundle\Fixture\LoadCurriculumInventoryExportData',
            'Tests\CoreBundle\Fixture\LoadCurriculumInventorySequenceData',
            'Tests\CoreBundle\Fixture\LoadCurriculumInventorySequenceBlockData',
            'Tests\CoreBundle\Fixture\LoadCurriculumInventoryAcademicLevelData'
        ]);
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [];
    }

    /**
     * @group controllers_a
     */
    public function testGetCurriculumInventoryReport()
    {
        $curriculumInventoryReport = $this->container
            ->get('ilioscore.dataloader.curriculuminventoryreport')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_curriculuminventoryreports',
                ['id' => $curriculumInventoryReport['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $returnedReport = json_decode($response->getContent(), true)['curriculumInventoryReports'][0];
        $this->assertNotEmpty($returnedReport['absoluteFileUri']);
        unset($returnedReport['absoluteFileUri']);
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($curriculumInventoryReport),
            $returnedReport
        );
    }

    /**
     * @group controllers_a
     */
    public function testGetAllCurriculumInventoryReports()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_curriculuminventoryreports'),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $expected = $this->mockSerialize(
            $this->container
                ->get('ilioscore.dataloader.curriculuminventoryreport')
                ->getAll()
        );
        $actual = json_decode($response->getContent(), true)['curriculumInventoryReports'];
        foreach ($actual as $returnedReport) {
            $this->assertNotEmpty($returnedReport['absoluteFileUri']);
        }
        array_walk($actual, function (&$returnedReport) {
            unset($returnedReport['absoluteFileUri']);
        });

        $this->assertEquals(
            $expected,
            $actual
        );
    }

    /**
     * @group controllers_a
     */
    public function testPostCurriculumInventoryReport()
    {
        $data = $this->container->get('ilioscore.dataloader.curriculuminventoryreport')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['sequence']);
        unset($postData['sequenceBlocks']);
        unset($postData['academicLevels']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventoryreports'),
            json_encode(['curriculumInventoryReport' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $responseData = json_decode($response->getContent(), true)['curriculumInventoryReports'][0];
        $this->assertNotEmpty($responseData['absoluteFileUri']);
        unset($responseData['absoluteFileUri']);
        $this->assertEquals(10, count($responseData['academicLevels']), 'There should be 10 academic levels ids.');
        $this->assertNotEmpty($responseData['sequence'], 'A sequence id should be present.');
        // don't compare sequence and academic level ids.
        $responseData['sequence'] = $data['sequence'];
        $responseData['academicLevels'] = $data['academicLevels'];
        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals($data, $responseData, $response->getContent());
    }

    /**
     * @group controllers_a
     */
    public function testPostBadCurriculumInventoryReport()
    {
        $invalidCurriculumInventoryReport = $this->container
            ->get('ilioscore.dataloader.curriculuminventoryreport')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventoryreports'),
            json_encode(['curriculumInventoryReport' => $invalidCurriculumInventoryReport]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers_a
     */
    public function testPutCurriculumInventoryReport()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.curriculuminventoryreport')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['sequenceBlocks']);
        unset($postData['academicLevels']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_curriculuminventoryreports',
                ['id' => $data['id']]
            ),
            json_encode(['curriculumInventoryReport' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $returnedReport = json_decode($response->getContent(), true)['curriculumInventoryReport'];
        $this->assertNotEmpty($returnedReport['absoluteFileUri']);
        unset($returnedReport['absoluteFileUri']);
        $this->assertEquals(
            $this->mockSerialize($data),
            $returnedReport
        );
    }

    /**
     * @group controllers
     */
    public function testDeleteCurriculumInventoryReport()
    {
        $curriculumInventoryReport = $this->container
            ->get('ilioscore.dataloader.curriculuminventoryreport')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_curriculuminventoryreports',
                ['id' => $curriculumInventoryReport['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_curriculuminventoryreports',
                ['id' => $curriculumInventoryReport['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testCurriculumInventoryReportNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_curriculuminventoryreports', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }

    /**
     * @group controllers_a
     */
    public function testRolloverCurriculumInventoryReport()
    {
        $report = $this->container
            ->get('ilioscore.dataloader.curriculuminventoryreport')
            ->getOne()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl(
                'api_curriculuminventoryreport_rollover_v1',
                [
                    'id' => $report['id'],
                ]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $data = json_decode($response->getContent(), true)['curriculumInventoryReports'];
        $newReport = $data[0];

        // compare reports
        $this->assertSame($report['name'], $newReport['name']);
        $this->assertSame($report['description'], $newReport['description']);
        $this->assertSame($report['year'], $newReport['year']);
        $this->assertSame($report['program'], $newReport['program']);
        $this->assertSame($report['startDate'], $newReport['startDate']);
        $this->assertSame($report['endDate'], $newReport['endDate']);
        $this->assertEmpty($newReport['export']);
        $this->assertNotEmpty($newReport['absoluteFileUri']);

        // compare sequences
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_curriculuminventorysequences', ['id' => $newReport['sequence']]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $newSequence = json_decode($response->getContent(), true)['curriculumInventorySequences'][0];
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_curriculuminventorysequences', ['id' => $report['sequence']]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $sequence = json_decode($response->getContent(), true)['curriculumInventorySequences'][0];
        $this->assertSame($sequence['description'], $newSequence['description']);

        // map and compare academic levels
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'cget_curriculuminventoryacademiclevels',
                ['filters[report]' => $report['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $levels = json_decode($response->getContent(), true)['curriculumInventoryAcademicLevels'];
        $levelsById = [];
        $levelsByLevel = [];
        foreach ($levels as $level) {
            $levelsById[$level['id']] = $level;
            $levelsByLevel[$level['level']] = $level; // "Bleed with +1 bleed."
        }

        // map and compare academic levels
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'cget_curriculuminventoryacademiclevels',
                ['filters[report]' => $newReport['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $newLevels = json_decode($response->getContent(), true)['curriculumInventoryAcademicLevels'];
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
        // @todo implement. [ST 2016/10/13]
    }
}
