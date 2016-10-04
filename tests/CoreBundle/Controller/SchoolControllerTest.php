<?php

namespace Tests\CoreBundle\Controller;

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
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Tests\CoreBundle\Fixture\LoadSchoolData',
            'Tests\CoreBundle\Fixture\LoadAlertData',
            'Tests\CoreBundle\Fixture\LoadCompetencyData',
            'Tests\CoreBundle\Fixture\LoadSessionTypeData',
            'Tests\CoreBundle\Fixture\LoadDepartmentData',
            'Tests\CoreBundle\Fixture\LoadCurriculumInventoryInstitutionData',
            'Tests\CoreBundle\Fixture\LoadProgramYearStewardData',
            'Tests\CoreBundle\Fixture\LoadCourseData',
            'Tests\CoreBundle\Fixture\LoadReportData',
            'Tests\CoreBundle\Fixture\LoadInstructorGroupData',
        ]);
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
            'templatePrefix',
            'alerts'
        ];
    }

    /**
     * @group controllers_b
     */
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
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($school),
            json_decode($response->getContent(), true)['schools'][0]
        );
    }

    /**
     * @group controllers_b
     */
    public function testGetAllSchools()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_schools'),
            null,
            $this->getAuthenticatedUserToken()
        );
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

    /**
     * @group controllers_b
     */
    public function testPostSchool()
    {
        $data = $this->container->get('ilioscore.dataloader.school')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['courses']);
        unset($postData['vocabularies']);
        unset($postData['departments']);
        unset($postData['programs']);
        unset($postData['competencies']);
        unset($postData['instructorGroups']);
        unset($postData['stewards']);
        unset($postData['sessionTypes']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_schools'),
            json_encode(['school' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['schools'][0],
            $response->getContent()
        );
    }

    /**
     * @group controllers_b
     */
    public function testPostBadSchool()
    {
        $invalidSchool = $this->container
            ->get('ilioscore.dataloader.school')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_schools'),
            json_encode(['school' => $invalidSchool]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers_b
     */
    public function testPutSchool()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.school')
            ->getOne();
        $data['curriculumInventoryInstitution'] = '2';


        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['courses']);
        unset($postData['programs']);
        unset($postData['vocabularies']);
        unset($postData['departments']);
        unset($postData['competencies']);
        unset($postData['instructorGroups']);
        unset($postData['stewards']);
        unset($postData['sessionTypes']);
        unset($postData['alerts']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_schools',
                ['id' => $data['id']]
            ),
            json_encode(['school' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['school']
        );
    }

    /**
     * @group controllers
     */
    public function testDeleteSchool()
    {
        $school = $this->container
            ->get('ilioscore.dataloader.school')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_schools',
                ['id' => $school['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_schools',
                ['id' => $school['id']]
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
    public function testSchoolNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_schools', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
