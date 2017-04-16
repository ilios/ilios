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
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadDepartmentData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSchoolData'
        ];
    }

    public function testGetDepartment()
    {
        $department = $this->container
            ->get('ilioscore.dataloader.department')
            ->getOne()['department']
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
            $department,
            json_decode($response->getContent(), true)['department']
        );
    }

    public function testGetAllDepartments()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_departments'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.department')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostDepartment()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_departments'),
            json_encode(
                $this->container->get('ilioscore.dataloader.department')
                    ->create()['department']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadDepartment()
    {
        $invalidDepartment = array_shift(
            $this->container->get('ilioscore.dataloader.department')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_departments'),
            $invalidDepartment
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutDepartment()
    {
        $department = $this->container
            ->get('ilioscore.dataloader.department')
            ->createWithId()['department']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_departments',
                ['id' => $department['id']]
            ),
            json_encode($department)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.department')
                ->getLastCreated()['department'],
            json_decode($response->getContent(), true)['department']
        );
    }

    public function testDeleteDepartment()
    {
        $department = $this->container
            ->get('ilioscore.dataloader.department')
            ->createWithId()['department']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_departments',
                ['id' => $department['id']]
            ),
            json_encode($department)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
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
            $this->getUrl('get_departments', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
