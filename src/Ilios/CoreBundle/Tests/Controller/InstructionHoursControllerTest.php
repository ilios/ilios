<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * InstructionHours controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class InstructionHoursControllerTest extends AbstractControllerTest
{
    /**
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadInstructionHoursData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionData'
        ];
    }

    public function testGetInstructionHours()
    {
        $instructionHours = $this->container
            ->get('ilioscore.dataloader.instructionhours')
            ->getOne()['instructionHours']
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_instructionhours',
                ['id' => $instructionHours['id']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $instructionHours,
            json_decode($response->getContent(), true)['instructionHours']
        );
    }

    public function testGetAllInstructionHours()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_instructionhours'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.instructionhours')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostInstructionHours()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_instructionhours'),
            json_encode(
                $this->container->get('ilioscore.dataloader.instructionhours')
                    ->create()['instructionHours']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadInstructionHours()
    {
        $invalidInstructionHours = array_shift(
            $this->container->get('ilioscore.dataloader.instructionhours')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_instructionhours'),
            $invalidInstructionHours
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutInstructionHours()
    {
        $instructionHours = $this->container
            ->get('ilioscore.dataloader.instructionhours')
            ->createWithId()['instructionHours']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_instructionhours',
                ['id' => $instructionHours['id']]
            ),
            json_encode($instructionHours)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.instructionhours')
                ->getLastCreated()['instructionHours'],
            json_decode($response->getContent(), true)['instructionHours']
        );
    }

    public function testDeleteInstructionHours()
    {
        $instructionHours = $this->container
            ->get('ilioscore.dataloader.instructionhours')
            ->createWithId()['instructionHours']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_instructionhours',
                ['id' => $instructionHours['id']]
            ),
            json_encode($instructionHours)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_instructionhours',
                ['id' => $instructionHours['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_instructionhours',
                ['id' => $instructionHours['id']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testInstructionHoursNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_instructionhours', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
