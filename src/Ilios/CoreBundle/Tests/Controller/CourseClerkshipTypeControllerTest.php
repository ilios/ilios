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
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseClerkshipTypeData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCourseData'
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
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($courseClerkshipType),
            json_decode($response->getContent(), true)['courseClerkshipTypes'][0]
        );
    }

    /**
     * @group controllers
     */
    public function testGetAllCourseClerkshipTypes()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('cget_courseclerkshiptypes'),
            null,
            $this->getAuthenticatedUserToken()
        );
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

    /**
     * @group controllers
     */
    public function testPostCourseClerkshipType()
    {
        $data = $this->container->get('ilioscore.dataloader.courseclerkshiptype')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['courses']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_courseclerkshiptypes'),
            json_encode(['courseClerkshipType' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['courseClerkshipTypes'][0],
            $response->getContent()
        );
    }

    /**
     * @group controllers
     */
    public function testPostBadCourseClerkshipType()
    {
        $invalidCourseClerkshipType = $this->container
            ->get('ilioscore.dataloader.courseclerkshiptype')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_courseclerkshiptypes'),
            json_encode(['courseClerkshipType' => $invalidCourseClerkshipType]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testPutCourseClerkshipType()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.courseclerkshiptype')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['courses']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_courseclerkshiptypes',
                ['id' => $data['id']]
            ),
            json_encode(['courseClerkshipType' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['courseClerkshipType']
        );
    }

    /**
     * @group controllers
     */
    public function testDeleteCourseClerkshipType()
    {
        $courseClerkshipType = $this->container
            ->get('ilioscore.dataloader.courseclerkshiptype')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_courseclerkshiptypes',
                ['id' => $courseClerkshipType['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_courseclerkshiptypes',
                ['id' => $courseClerkshipType['id']]
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
    public function testCourseClerkshipTypeNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_courseclerkshiptypes', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
