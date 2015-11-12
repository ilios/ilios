<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * Course controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class CourseControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseClerkshipTypeData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSchoolData',
            'Ilios\CoreBundle\Tests\Fixture\LoadPublishEventData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCohortData',
            'Ilios\CoreBundle\Tests\Fixture\LoadTopicData',
            'Ilios\CoreBundle\Tests\Fixture\LoadObjectiveData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseLearningMaterialData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadOfferingData',
            'Ilios\CoreBundle\Tests\Fixture\LoadProgramYearData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionLearningMaterialData',
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
    public function testGetCourse()
    {
        $course = $this->container
            ->get('ilioscore.dataloader.course')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_courses',
                ['id' => $course['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);

        $this->assertEquals(
            $this->mockSerialize($course),
            json_decode($response->getContent(), true)['courses'][0]
        );
    }

    /**
     * @group controllers
     */
    public function testGetAllCourses()
    {
        $courses = $this->container->get('ilioscore.dataloader.course')->getAll();

        $this->createJsonRequest('GET', $this->getUrl('cget_courses'), null, $this->getAuthenticatedUserToken());
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $courses
            ),
            json_decode($response->getContent(), true)['courses']
        );
    }

    /**
     * @group controllers
     */
    public function testPostCourse()
    {
        $data = $this->container->get('ilioscore.dataloader.course')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['learningMaterials']);
        unset($postData['sessions']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_courses'),
            json_encode(['course' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['courses'][0],
            $response->getContent()
        );
    }

    /**
     * @group controllers
     */
    public function testPostBadCourse()
    {
        $invalidCourse = $this->container
            ->get('ilioscore.dataloader.course')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_courses'),
            json_encode(['course' => $invalidCourse]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testPutCourse()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.course')
            ->getOne();
        
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['learningMaterials']);
        unset($postData['sessions']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_courses',
                ['id' => $data['id']]
            ),
            json_encode(['course' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['course']
        );
    }

    /**
     * @group controllers
     */
    public function testDeleteCourse()
    {
        $course = $this->container
            ->get('ilioscore.dataloader.course')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_courses',
                ['id' => $course['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_courses',
                ['id' => $course['id']]
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
    public function testCourseNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_courses', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }

    /**
     * @group controllers
     */
    public function testFilterByLevel()
    {
        $courses = $this->container->get('ilioscore.dataloader.course')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_courses', ['filters[level]' => 3]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['courses'];
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $courses[3]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByYear()
    {
        $courses = $this->container->get('ilioscore.dataloader.course')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_courses', ['filters[year]' => 2012]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['courses'];
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $courses[1]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByIds()
    {
        $courses = $this->container->get('ilioscore.dataloader.course')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_courses', ['filters[id]' => [1,3]]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['courses'];
        $this->assertEquals(2, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $courses[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $courses[2]
            ),
            $data[1]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByTopic()
    {
        $courses = $this->container->get('ilioscore.dataloader.course')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_courses', ['filters[topics][]' => 1]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['courses'];
        $this->assertEquals(1, count($data));
        $this->assertEquals(
            $this->mockSerialize(
                $courses[1]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterBySession()
    {
        $courses = $this->container->get('ilioscore.dataloader.course')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_courses', ['filters[sessions][]' => 3]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['courses'];
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $courses[1]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByProgram()
    {
        $courses = $this->container->get('ilioscore.dataloader.course')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_courses', ['filters[programs][]' => 2]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['courses'];
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $courses[3]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByInstructor()
    {
        $courses = $this->container->get('ilioscore.dataloader.course')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_courses', ['filters[instructors]' => [1,2]]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['courses'];
        $this->assertEquals(2, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $courses[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $courses[1]
            ),
            $data[1]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByProgramYear()
    {
        $courses = $this->container->get('ilioscore.dataloader.course')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_courses', ['filters[programYears][]' => 2]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['courses'];
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $courses[2]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByInstructorGroup()
    {
        $courses = $this->container->get('ilioscore.dataloader.course')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_courses', ['filters[instructorGroups][]' => 1]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['courses'];
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $courses[0]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByLearningMaterial()
    {
        $courses = $this->container->get('ilioscore.dataloader.course')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_courses', ['filters[learningMaterials]' => [1,3]]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['courses'];
        $this->assertEquals(2, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $courses[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $courses[1]
            ),
            $data[1]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByCompetency()
    {
        $courses = $this->container->get('ilioscore.dataloader.course')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_courses', ['filters[competencies][]' => 2]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['courses'];
        $this->assertEquals(1, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $courses[1]
            ),
            $data[0]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByMeshDescriptor()
    {
        $courses = $this->container->get('ilioscore.dataloader.course')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_courses', ['filters[meshDescriptors]' => ['abc1', 'abc2']]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['courses'];
        $this->assertEquals(4, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $courses[0]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $courses[1]
            ),
            $data[1]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $courses[2]
            ),
            $data[2]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $courses[3]
            ),
            $data[3]
        );
    }
}
