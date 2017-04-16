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
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseClerkshipTypeData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSchoolData',
            'Ilios\CoreBundle\Tests\Fixture\LoadPublishEventData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCohortData',
            'Ilios\CoreBundle\Tests\Fixture\LoadDisciplineData',
            'Ilios\CoreBundle\Tests\Fixture\LoadObjectiveData',
            'Ilios\CoreBundle\Tests\Fixture\LoadMeshDescriptorData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseLearningMaterialData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionData'
        ];
    }

    public function testGetCourse()
    {
        $course = $this->container
            ->get('ilioscore.dataloader.course')
            ->getOne()['course']
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_courses',
                ['id' => $course['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $course,
            json_decode($response->getContent(), true)['course']
        );
    }

    public function testGetAllCourses()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_courses'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.course')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostCourse()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_courses'),
            json_encode(
                $this->container->get('ilioscore.dataloader.course')
                    ->create()['course']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadCourse()
    {
        $invalidCourse = array_shift(
            $this->container->get('ilioscore.dataloader.course')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_courses'),
            $invalidCourse
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutCourse()
    {
        $course = $this->container
            ->get('ilioscore.dataloader.course')
            ->createWithId()['course']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_courses',
                ['id' => $course['id']]
            ),
            json_encode($course)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.course')
                ->getLastCreated()['course'],
            json_decode($response->getContent(), true)['course']
        );
    }

    public function testDeleteCourse()
    {
        $course = $this->container
            ->get('ilioscore.dataloader.course')
            ->createWithId()['course']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_courses',
                ['id' => $course['id']]
            ),
            json_encode($course)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_courses',
                ['id' => $course['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_courses',
                ['id' => $course['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testCourseNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_courses', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
