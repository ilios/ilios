<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * CourseClerkshipType controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class CourseClerkshipTypeControllerTest extends AbstractControllerTest
{
    /**
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseClerkshipTypeData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseData'
        ];
    }

    public function testGetCourseClerkshipType()
    {
        $courseClerkshipType = $this->container
            ->get('ilioscore.dataloader.courseclerkshiptype')
            ->getOne()['courseClerkshipType']
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_courseclerkshiptypes',
                ['id' => $courseClerkshipType['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $courseClerkshipType,
            json_decode($response->getContent(), true)['courseClerkshipType']
        );
    }

    public function testGetAllCourseClerkshipTypes()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_courseclerkshiptypes'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.courseclerkshiptype')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostCourseClerkshipType()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_courseclerkshiptypes'),
            json_encode(
                $this->container->get('ilioscore.dataloader.courseclerkshiptype')
                    ->create()['courseClerkshipType']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadCourseClerkshipType()
    {
        $invalidCourseClerkshipType = array_shift(
            $this->container->get('ilioscore.dataloader.courseclerkshiptype')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_courseclerkshiptypes'),
            $invalidCourseClerkshipType
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutCourseClerkshipType()
    {
        $courseClerkshipType = $this->container
            ->get('ilioscore.dataloader.courseclerkshiptype')
            ->createWithId()['courseClerkshipType']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_courseclerkshiptypes',
                ['id' => $courseClerkshipType['id']]
            ),
            json_encode($courseClerkshipType)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.courseclerkshiptype')
                ->getLastCreated()['courseClerkshipType'],
            json_decode($response->getContent(), true)['courseClerkshipType']
        );
    }

    public function testDeleteCourseClerkshipType()
    {
        $courseClerkshipType = $this->container
            ->get('ilioscore.dataloader.courseclerkshiptype')
            ->createWithId()['courseClerkshipType']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_courseclerkshiptypes',
                ['id' => $courseClerkshipType['id']]
            ),
            json_encode($courseClerkshipType)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_courseclerkshiptypes',
                ['id' => $courseClerkshipType['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_courseclerkshiptypes',
                ['id' => $courseClerkshipType['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testCourseClerkshipTypeNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_courseclerkshiptypes', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
