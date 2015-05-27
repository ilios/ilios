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
     * @return array
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

    public function testGetSchool()
    {
        $school = $this->container
            ->get('ilioscore.dataloader.school')
            ->getOne()['school']
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
            $school,
            json_decode($response->getContent(), true)['school']
        );
    }

    public function testGetAllSchools()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_schools'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.school')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostSchool()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_schools'),
            json_encode(
                $this->container->get('ilioscore.dataloader.school')
                    ->create()['school']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadSchool()
    {
        $invalidSchool = array_shift(
            $this->container->get('ilioscore.dataloader.school')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_schools'),
            $invalidSchool
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutSchool()
    {
        $school = $this->container
            ->get('ilioscore.dataloader.school')
            ->createWithId()['school']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_schools',
                ['id' => $school['id']]
            ),
            json_encode($school)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.school')
                ->getLastCreated()['school'],
            json_decode($response->getContent(), true)['school']
        );
    }

    public function testDeleteSchool()
    {
        $school = $this->container
            ->get('ilioscore.dataloader.school')
            ->createWithId()['school']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_schools',
                ['id' => $school['id']]
            ),
            json_encode($school)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
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
            $this->getUrl('get_schools', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
