<?php

namespace Tests\CoreBundle\Controller;

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
            'Tests\CoreBundle\Fixture\LoadCourseData',
            'Tests\CoreBundle\Fixture\LoadCourseClerkshipTypeData',
            'Tests\CoreBundle\Fixture\LoadSchoolData',
            'Tests\CoreBundle\Fixture\LoadUserData',
            'Tests\CoreBundle\Fixture\LoadCohortData',
            'Tests\CoreBundle\Fixture\LoadTermData',
            'Tests\CoreBundle\Fixture\LoadObjectiveData',
            'Tests\CoreBundle\Fixture\LoadCourseLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadSessionData',
            'Tests\CoreBundle\Fixture\LoadSessionDescriptionData',
            'Tests\CoreBundle\Fixture\LoadOfferingData',
            'Tests\CoreBundle\Fixture\LoadProgramYearData',
            'Tests\CoreBundle\Fixture\LoadSessionLearningMaterialData',
            'Tests\CoreBundle\Fixture\LoadIlmSessionData',
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
     * @group controllers_a
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
     * @group controllers_a
     */
    public function testGetMyCourses()
    {
        $courses = $this->container->get('ilioscore.dataloader.course')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_courses', ['my' => true]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['courses'];
        $this->assertEquals(3, count($data), var_export($data, true));
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
                $courses[3]
            ),
            $data[2]
        );
    }

    /**
     * @group controllers_a
     */
    public function testGetMyCoursesSorted()
    {
        $courses = $this->container->get('ilioscore.dataloader.course')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_courses', ['my' => true, 'order_by[year]' => 'ASC', 'order_by[id]' => 'DESC']),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['courses'];
        $this->assertEquals(3, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $courses[1]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $courses[3]
            ),
            $data[1]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $courses[0]
            ),
            $data[2]
        );
    }

    /**
     * @group controllers_a
     */
    public function testGetMyCoursesFailureOnBogusField()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_courses', ['my' => true, 'order_by[glefarknik]' => 'ASC']),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_INTERNAL_SERVER_ERROR);

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_courses', ['my' => true, 'filters[farnk]' => 1]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * @group controllers_a
     */
    public function testGetMyCoursesWithLimitAndOffset()
    {
        $courses = $this->container->get('ilioscore.dataloader.course')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_courses', ['my' => true, 'limit' => 2]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['courses'];
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

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['courses'];
        $this->assertEquals(2, count($data), var_export($data, true));
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_courses', ['my' => true, 'limit' => 1, 'offset' => 1]),
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
     * @group controllers_a
     */
    public function testGetMyCoursesFilteredByYear()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_courses', ['my' => true, 'filters[year]' => '2012']),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['courses'];
        $this->assertEquals(1, count($data), var_export($data, true));

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_courses', ['my' => true, 'filters[year]' => '2013']),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['courses'];
        $this->assertEquals(1, count($data), var_export($data, true));

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_courses', ['my' => true, 'filters[year]' => ['2012', '2013']]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['courses'];
        $this->assertEquals(2, count($data), var_export($data, true));
    }

    /**
     * @group controllers_a
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
     * @group controllers_a
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
     * @group controllers_a
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
     * @group controllers_a
     */
    public function testPutCourse()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.course')
            ->getOne();

        $data['objectives'] = ['3'];
        $data['directors'] = ['2'];
        $data['administrators'] = ['2'];
        $data['cohorts'] = ['2'];
        $data['meshDescriptors'] = ['abc2'];
        $data['descendants'] = ['3'];
        $data['terms'] = ['2'];

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
     * Ember doesn't send the non-owning side of many2one relationships
     * @group controllers_a
     */
    public function testPutCourseWithoutSessionsAndLms()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.course')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['sessions']);
        unset($postData['learningMaterials']);

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
        $this->assertEquals(2, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $courses[1]
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
    public function testFilterByYearAndLevel()
    {
        $courses = $this->container->get('ilioscore.dataloader.course')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_courses', ['filters[year]' => 2013, 'filters[level]' => 3]),
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
    public function testFilterBySchool()
    {
        $courses = $this->container->get('ilioscore.dataloader.course')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_courses', ['filters[school]' => 2]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['courses'];
        $this->assertEquals(2, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $courses[2]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $courses[3]
            ),
            $data[1]
        );
    }

    /**
     * @group controllers
     */
    public function testFilterByYearAndSchool()
    {
        $courses = $this->container->get('ilioscore.dataloader.course')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_courses', ['filters[year]' => 2013, 'filters[school]' => 2]),
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
    public function testFilterByTerm()
    {
        $courses = $this->container->get('ilioscore.dataloader.course')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_courses', ['filters[terms][]' => 1]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['courses'];
        $this->assertEquals(2, count($data));
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
        $this->assertEquals(3, count($data), var_export($data, true));
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
                $courses[3]
            ),
            $data[2]
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
        $this->assertEquals(3, count($data), var_export($data, true));
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
                $courses[3]
            ),
            $data[2]
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
            $this->getUrl('cget_courses', ['filters[competencies][]' => 1]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['courses'];
        $this->assertEquals(3, count($data), var_export($data, true));
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
                $courses[3]
            ),
            $data[2]
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

    /**
     * @group controllers
     */
    public function testFilterBySchools()
    {
        $courses = $this->container->get('ilioscore.dataloader.course')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_courses', ['filters[schools]' => [2]]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['courses'];
        $this->assertEquals(2, count($data), var_export($data, true));
        $this->assertEquals(
            $this->mockSerialize(
                $courses[2]
            ),
            $data[0]
        );
        $this->assertEquals(
            $this->mockSerialize(
                $courses[3]
            ),
            $data[1]
        );
    }

    /**
     * @group controllers_a
     */
    public function testFilterByAncestor()
    {
        $courses = $this->container->get('ilioscore.dataloader.course')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_courses', ['filters[ancestor]' => 3]),
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
     * @group controllers_a
     */
    public function testFilterByAncestors()
    {
        $courses = $this->container->get('ilioscore.dataloader.course')->getAll();

        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_courses', ['filters[ancestors]' => [3]]),
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
     * @group controllers_a
     */
    public function testRolloverCourse()
    {
        $course = $this->container
            ->get('ilioscore.dataloader.course')
            ->getOne()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl(
                'api_course_rollover_v1',
                [
                    'id' => $course['id'],
                    'year' => 2019,
                    'newStartDate' => 'false',
                    'skipOfferings' => 'false',
                ]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $data = json_decode($response->getContent(), true)['courses'];
        $newCourse = $data[0];
        $this->assertSame($course['title'], $newCourse['title']);
        $this->assertSame($course['level'], $newCourse['level']);
        $this->assertSame($course['externalId'], $newCourse['externalId']);
        $this->assertSame(2019, $newCourse['year']);
        $this->assertSame('2019-09-01T00:00:00+00:00', $newCourse['startDate']);
        $this->assertSame('2019-12-29T00:00:00+00:00', $newCourse['endDate']);
        $this->assertFalse($newCourse['locked']);
        $this->assertFalse($newCourse['archived']);
        $this->assertFalse($newCourse['published']);
        $this->assertFalse($newCourse['publishedAsTbd']);

        $this->assertEquals($course['clerkshipType'], $newCourse['clerkshipType']);
        $this->assertEquals($course['school'], $newCourse['school']);
        $this->assertEquals($course['directors'], $newCourse['directors']);
        $this->assertEmpty($newCourse['cohorts']);
        $this->assertEquals($course['terms'], $newCourse['terms']);
        $this->assertSame(count($course['objectives']), count($newCourse['objectives']));
        $this->assertEquals($course['meshDescriptors'], $newCourse['meshDescriptors']);
        $this->assertSame(count($course['learningMaterials']), count($newCourse['learningMaterials']));

        $newSessions = $newCourse['sessions'];
        $this->assertEquals(count($newSessions), 2);
        $sessions = $this->container->get('ilioscore.dataloader.session')->getAll();
        $lastSessionId = array_pop($sessions)['id'];

        $this->assertEquals($lastSessionId + 1, $newSessions[0], 'incremented session id 1');
        $this->assertEquals($lastSessionId + 2, $newSessions[1], 'incremented session id 2');

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'cget_sessions',
                ['filters[id]' => $newSessions]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $newSessionsData = json_decode($response->getContent(), true)['sessions'];
        $offerings = $this->container->get('ilioscore.dataloader.offering')->getAll();
        $lastOfferingId = array_pop($offerings)['id'];

        $firstSessionOfferings = array_map('strval', [$lastOfferingId + 1, $lastOfferingId + 2]);
        $secondSessionOfferings = array_map('strval', [$lastOfferingId + 3, $lastOfferingId + 4, $lastOfferingId + 5]);

        $this->assertEquals($firstSessionOfferings, $newSessionsData[0]['offerings']);
        $this->assertEquals($secondSessionOfferings, $newSessionsData[1]['offerings']);

        $newDescriptionIds = array_map(function (array $session) {
            return $session['sessionDescription'];
        }, $newSessionsData);
        $this->assertEquals(count($newDescriptionIds), 2);
        $descriptions = $this->container->get('ilioscore.dataloader.sessiondescription')->getAll();
        $lastDescriptionId = $descriptions[1]['id'];

        $this->assertEquals($lastDescriptionId + 1, $newDescriptionIds[0], 'incremented description id 1');
        $this->assertEquals($lastDescriptionId + 2, $newDescriptionIds[1], 'incremented description id 2');

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'cget_sessiondescriptions',
                ['filters[id]' => $newDescriptionIds]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $newDescriptionData = json_decode($response->getContent(), true)['sessionDescriptions'];
        $this->assertEquals($newDescriptionData[0]['description'], $descriptions[0]['description']);
        $this->assertEquals($newDescriptionData[1]['description'], $descriptions[1]['description']);
    }

    /**
     * @group controllers_a
     */
    public function testRolloverCourseWithStartDate()
    {
        $course = $this->container
            ->get('ilioscore.dataloader.course')
            ->getOne()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl(
                'api_course_rollover_v1',
                [
                    'id' => $course['id'],
                    'year' => 2017,
                    'newStartDate' => '2017-02-05'
                ]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $data = json_decode($response->getContent(), true)['courses'];
        $newCourse = $data[0];
        $this->assertSame(2017, $newCourse['year']);
        $this->assertSame('2017-02-05T00:00:00+00:00', $newCourse['startDate'], 'start date is correct');
        $this->assertSame('2017-06-04T00:00:00+00:00', $newCourse['endDate'], 'end date is correct');

        $newSessions = $newCourse['sessions'];
        $this->assertEquals(count($newSessions), 2);

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'cget_sessions',
                ['filters[id]' => $newSessions]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $newSessionsData = json_decode($response->getContent(), true)['sessions'];

        $session1Offerings = $newSessionsData[0]['offerings'];
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'cget_offerings',
                ['filters[id]' => $session1Offerings]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $session1fferingData = json_decode($response->getContent(), true)['offerings'];

        $this->assertEquals('2017-02-09T15:00:00+00:00', $session1fferingData[0]['startDate']);
        $this->assertEquals('2017-02-08T17:00:00+00:00', $session1fferingData[1]['startDate']);
    }

    /**
     * @group controllers_a
     */
    public function testRolloverCourseWithNoOfferings()
    {
        $course = $this->container
            ->get('ilioscore.dataloader.course')
            ->getOne()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl(
                'api_course_rollover_v1',
                [
                    'id' => $course['id'],
                    'year' => 2030,
                    'skipOfferings' => true
                ]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $data = json_decode($response->getContent(), true)['courses'];
        $newCourse = $data[0];
        $this->assertSame(2030, $newCourse['year']);
        $newSessions = $newCourse['sessions'];
        $this->assertEquals(count($newSessions), 2);
        $sessions = $this->container->get('ilioscore.dataloader.session')->getAll();
        $lastSessionId = array_pop($sessions)['id'];

        $this->assertEquals($lastSessionId + 1, $newSessions[0], 'incremented session id 1');
        $this->assertEquals($lastSessionId + 2, $newSessions[1], 'incremented session id 2');

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'cget_sessions',
                ['filters[id]' => $newSessions]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $data = json_decode($response->getContent(), true)['sessions'];
        $this->assertEmpty($data[0]['offerings']);
        $this->assertEmpty($data[1]['offerings']);
    }

    /**
     * @group controllers_a
     */
    public function testRolloverCourseWithNewTitle()
    {
        $course = $this->container
            ->get('ilioscore.dataloader.course')
            ->getOne()
        ;
        $newCourseTitle = 'New (very cool) course title';

        $this->createJsonRequest(
            'POST',
            $this->getUrl(
                'api_course_rollover_v1',
                [
                    'id' => $course['id'],
                    'year' => $course['year'],
                    'newCourseTitle' => $newCourseTitle
                ]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $data = json_decode($response->getContent(), true)['courses'];
        $newCourse = $data[0];
        $this->assertSame($course['year'], $newCourse['year']);
        $this->assertSame($newCourseTitle, $newCourse['title']);
    }



    /**
     * @group controllers_a
     */
    public function testRolloverIlmSessions()
    {
        $courses = $this->container
            ->get('ilioscore.dataloader.course')
            ->getAll()
        ;
        $course = $courses[1];

        $this->createJsonRequest(
            'POST',
            $this->getUrl(
                'api_course_rollover_v1',
                [
                    'id' => $course['id'],
                    'year' => 2019,
                ]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $newCourse = json_decode($response->getContent(), true)['courses'][0];

        $newSessionIds = $newCourse['sessions'];
        $this->assertEquals(count($newSessionIds), 5);

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'cget_sessions',
                ['filters[id]' => $newSessionIds]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $newSessionData = json_decode($response->getContent(), true)['sessions'];


        $newSessionsWithILMs = array_filter($newSessionData, function (array $session) {
            return !empty($session['ilmSession']);
        });
        $this->assertEquals(4, count($newSessionsWithILMs));

        $newIlmIds = array_map(function (array $session) {
            return $session['ilmSession'];
        }, $newSessionsWithILMs);
        $newIlmIds = array_values($newIlmIds);

        $ilms = $this->container->get('ilioscore.dataloader.ilmsession')->getAll();
        $lastIlmId = $ilms[key(array_slice($ilms, -1, 1, true))]['id'];

        $this->assertEquals($lastIlmId + 1, $newIlmIds[0], 'incremented ilm id 1');
        $this->assertEquals($lastIlmId + 2, $newIlmIds[1], 'incremented ilm id 2');
        $this->assertEquals($lastIlmId + 3, $newIlmIds[2], 'incremented ilm id 3');
        $this->assertEquals($lastIlmId + 4, $newIlmIds[3], 'incremented ilm id 4');

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'cget_ilmsessions',
                ['filters[id]' => $newIlmIds]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $newIlmData = json_decode($response->getContent(), true)['ilmSessions'];
        $this->assertEquals($newIlmData[0]['hours'], $ilms[0]['hours']);
        $this->assertEquals($newIlmData[1]['hours'], $ilms[1]['hours']);
    }

    /**
     * @group controllers_a
     */
    public function testRejectUnpriviledged()
    {
        $subject = $this->container
            ->get('ilioscore.dataloader.course')
            ->getOne()
        ;
        //unset any parameters which should not be POSTed
        $id = $subject['id'];
        unset($subject['id']);
        unset($subject['learningMaterials']);
        unset($subject['sessions']);
        $userId = 3;

        $this->canNot($userId, 'POST', $this->getUrl('post_courses'), json_encode(['course' => $subject]));
        $this->canNot($userId, 'PUT', $this->getUrl('put_courses', ['id' => $id]), json_encode(['course' => $subject]));
        $this->canNot(
            $userId,
            'PUT',
            $this->getUrl('put_courses', ['id' => $id * 10000]),
            json_encode(['course' => $subject])
        );
        $this->canNot($userId, 'DELETE', $this->getUrl('delete_courses', ['id' => $id]));
        $rolloverData = [
            'id' => $id,
            'year' => 2019,
            'newStartDate' => 'false',
            'skipOfferings' => 'false',
        ];
        $this->canNot($userId, 'POST', $this->getUrl('api_course_rollover_v1', $rolloverData));
    }

    /**
     * @group controllers_a
     */
    public function testCourseCanBeUnlocked()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.course')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['learningMaterials']);
        unset($postData['sessions']);

        //lock course
        $postData['locked'] = true;

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
        $this->assertTrue(
            json_decode($response->getContent(), true)['course']['locked']
        );

        //unlock course
        $postData['locked'] = false;
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
        $this->assertFalse(
            json_decode($response->getContent(), true)['course']['locked']
        );
    }

    /**
     * @group controllers_a
     */
    public function testRemovingCourseObjectiveRemovesSessionObjectivesToo()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.course')
            ->getOne();
        $objectiveId = $data['objectives'][0];
        $data['objectives'] = [];

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

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_objectives',
                ['id' => $objectiveId]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $result = json_decode($response->getContent(), true)['objectives'][0];
        $this->assertEquals($result['children'], ['6']);
    }
}
