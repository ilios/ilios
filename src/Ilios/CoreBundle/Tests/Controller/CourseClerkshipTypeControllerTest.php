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
     * @return array|string
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseClerkshipTypeData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseData'
        ];
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
        ];
    }

    public function testGetCourseClerkshipType()
    {
        $courseClerkshipType = $this->container
            ->get('ilioscore.dataloader.courseclerkshiptype')
            ->getOne()
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
            $this->mockSerialize($courseClerkshipType),
            json_decode($response->getContent(), true)['courseClerkshipTypes'][0]
        );
    }

    public function testGetAllCourseClerkshipTypes()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_courseclerkshiptypes'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.courseclerkshiptype')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['courseClerkshipTypes']
        );
    }

    public function testPostCourseClerkshipType()
    {
        $data = $this->container->get('ilioscore.dataloader.courseclerkshiptype')
            ->create();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_courseclerkshiptypes'),
            json_encode(['courseClerkshipType' => $data])
        );

        $response = $this->client->getResponse();
        $headers  = [];

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode());
        $this->assertTrue(
            $response->headers->contains(
                'Location'
            ),
            print_r($response->headers, true)
        );
    }

    public function testPostBadCourseClerkshipType()
    {
        $invalidCourseClerkshipType = $this->container
            ->get('ilioscore.dataloader.courseclerkshiptype')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_courseclerkshiptypes'),
            json_encode(['courseClerkshipType' => $invalidCourseClerkshipType])
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutCourseClerkshipType()
    {
        $courseClerkshipType = $this->container
            ->get('ilioscore.dataloader.courseclerkshiptype')
            ->getOne()
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_courseclerkshiptypes',
                ['id' => $courseClerkshipType['id']]
            ),
            json_encode(['courseClerkshipType' => $courseClerkshipType])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($courseClerkshipType),
            json_decode($response->getContent(), true)['courseClerkshipType']
        );
    }

    public function testDeleteCourseClerkshipType()
    {
        $courseClerkshipType = $this->container
            ->get('ilioscore.dataloader.courseclerkshiptype')
            ->getOne()
        ;

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
            $this->getUrl('get_courseclerkshiptypes', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
