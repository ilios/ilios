<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * Program controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class ProgramControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadProgramData',
            'Ilios\CoreBundle\Tests\Fixture\LoadPublishEventData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSchoolData',
            'Ilios\CoreBundle\Tests\Fixture\LoadProgramYearData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventoryReportData'
        ]);
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
        ];
    }

    /**
     * @group controllers
     */
    public function testGetProgram()
    {
        $program = $this->container
            ->get('ilioscore.dataloader.program')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_programs',
                ['id' => $program['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($program),
            json_decode($response->getContent(), true)['programs'][0]
        );
    }

    /**
     * @group controllers
     */
    public function testGetAllPrograms()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_programs'),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.program')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['programs']
        );
    }

    /**
     * @group controllers
     */
    public function testPostProgram()
    {
        $data = $this->container->get('ilioscore.dataloader.program')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['programYears']);
        unset($postData['curriculumInventoryReports']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_programs'),
            json_encode(['program' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['programs'][0],
            $response->getContent()
        );
    }

    /**
     * @group controllers
     */
    public function testPostBadProgram()
    {
        $invalidProgram = $this->container
            ->get('ilioscore.dataloader.program')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_programs'),
            json_encode(['program' => $invalidProgram]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testPutProgram()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.program')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['programYears']);
        unset($postData['curriculumInventoryReports']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_programs',
                ['id' => $data['id']]
            ),
            json_encode(['program' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['program']
        );
    }

    /**
     * @group controllers
     */
    public function testDeleteProgram()
    {
        $program = $this->container
            ->get('ilioscore.dataloader.program')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_programs',
                ['id' => $program['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_programs',
                ['id' => $program['id']]
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
    public function testProgramNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_programs', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
