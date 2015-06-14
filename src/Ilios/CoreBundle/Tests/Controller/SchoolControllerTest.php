<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * School controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class SchoolControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadSchoolData',
            'Ilios\CoreBundle\Tests\Fixture\LoadAlertData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCompetencyData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseData',
            'Ilios\CoreBundle\Tests\Fixture\LoadProgramData',
            'Ilios\CoreBundle\Tests\Fixture\LoadDepartmentData',
            'Ilios\CoreBundle\Tests\Fixture\LoadDisciplineData',
            'Ilios\CoreBundle\Tests\Fixture\LoadInstructorGroupData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventoryInstitutionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionTypeData'
        ];
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
            'templatePrefix'
        ];
    }

    public function testGetSchool()
    {
        $school = $this->container
            ->get('ilioscore.dataloader.school')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_schools',
                ['id' => $school['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($school),
            json_decode($response->getContent(), true)['schools'][0]
        );
    }

    public function testGetAllSchools()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_schools'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.school')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['schools']
        );
    }

    public function testPostSchool()
    {
        $data = $this->container->get('ilioscore.dataloader.school')
            ->create();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_schools'),
            json_encode(['school' => $data])
        );

        $response = $this->client->getResponse();
        $headers  = [];

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['schools'][0]
        );
    }

    public function testPostBadSchool()
    {
        $invalidSchool = $this->container
            ->get('ilioscore.dataloader.school')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_schools'),
            json_encode(['school' => $invalidSchool])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testPutSchool()
    {
        $school = $this->container
            ->get('ilioscore.dataloader.school')
            ->getOne()
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_schools',
                ['id' => $school['id']]
            ),
            json_encode(['school' => $school])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($school),
            json_decode($response->getContent(), true)['school']
        );
    }

    public function testDeleteSchool()
    {
        $school = $this->container
            ->get('ilioscore.dataloader.school')
            ->getOne()
        ;

        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_schools',
                ['id' => $school['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_schools',
                ['id' => $school['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testSchoolNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_schools', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
