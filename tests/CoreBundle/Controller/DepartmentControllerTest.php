<?php

namespace Tests\CoreBundle\Controller;

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
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadDepartmentData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSchoolData',
            'Ilios\CoreBundle\Tests\Fixture\LoadProgramYearStewardData'
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
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($department),
            json_decode($response->getContent(), true)['departments'][0]
        );
    }

    /**
     * @group controllers_a
     */
    public function testGetAllDepartments()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_departments'), null, $this->getAuthenticatedUserToken());
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

    /**
     * @group controllers_a
     */
    public function testPostDepartment()
    {
        $data = $this->container->get('ilioscore.dataloader.department')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['stewards']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_departments'),
            json_encode(['department' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['departments'][0],
            $response->getContent()
        );
    }

    /**
     * @group controllers_a
     */
    public function testPostBadDepartment()
    {
        $invalidDepartment = $this->container
            ->get('ilioscore.dataloader.department')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_departments'),
            json_encode(['department' => $invalidDepartment]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers_a
     */
    public function testPutDepartment()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.department')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['stewards']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_departments',
                ['id' => $data['id']]
            ),
            json_encode(['department' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['department']
        );
    }

    /**
     * @group controllers
     */
    public function testDeleteDepartment()
    {
        $department = $this->container
            ->get('ilioscore.dataloader.department')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_departments',
                ['id' => $department['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_departments',
                ['id' => $department['id']]
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
    public function testDepartmentNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_departments', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
