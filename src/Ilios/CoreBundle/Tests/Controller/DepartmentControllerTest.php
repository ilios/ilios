<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * Department controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class DepartmentControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadDepartmentData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSchoolData'
        ];
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
            'title',
            'deleted'
        ];
    }

    public function testGetDepartment()
    {
        $department = $this->container
            ->get('ilioscore.dataloader.department')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_departments',
                ['id' => $department['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($department),
            json_decode($response->getContent(), true)['departments'][0]
        );
    }

    public function testGetAllDepartments()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_departments'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.department')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['departments']
        );
    }

    public function testPostDepartment()
    {
        $data = $this->container->get('ilioscore.dataloader.department')
            ->create();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_departments'),
            json_encode(['department' => $data])
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

    public function testPostBadDepartment()
    {
        $invalidDepartment = $this->container
            ->get('ilioscore.dataloader.department')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_departments'),
            json_encode(['department' => $invalidDepartment])
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutDepartment()
    {
        $department = $this->container
            ->get('ilioscore.dataloader.department')
            ->getOne()
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_departments',
                ['id' => $department['id']]
            ),
            json_encode(['department' => $department])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($department),
            json_decode($response->getContent(), true)['department']
        );
    }

    public function testDeleteDepartment()
    {
        $department = $this->container
            ->get('ilioscore.dataloader.department')
            ->getOne()
        ;

        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_departments',
                ['id' => $department['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_departments',
                ['id' => $department['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testDepartmentNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_departments', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
