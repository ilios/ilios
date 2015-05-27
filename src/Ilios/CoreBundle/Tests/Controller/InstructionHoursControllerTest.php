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
     * @return array|string
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadInstructionHoursData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSessionData'
        ];
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
            'createdAt',
            'hoursAccrued',
            'modified',
            'updatedAt'
        ];
    }

    public function testGetInstructionHours()
    {
        $instructionHours = $this->container
            ->get('ilioscore.dataloader.instructionhours')
            ->getOne()
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
            $this->mockSerialize($instructionHours),
            json_decode($response->getContent(), true)['instructionHours'][0]
        );
    }

    public function testGetAllInstructionHours()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_instructionhours'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.instructionhours')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['instructionHours']
        );
    }

    public function testPostInstructionHours()
    {
        $data = $this->container->get('ilioscore.dataloader.instructionhours')
            ->create();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_instructionhours'),
            json_encode(['instructionHours' => $data])
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

    public function testPostBadInstructionHours()
    {
        $invalidInstructionHours = $this->container
            ->get('ilioscore.dataloader.instructionhours')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_instructionhours'),
            json_encode(['instructionHours' => $invalidInstructionHours])
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutInstructionHours()
    {
        $instructionHours = $this->container
            ->get('ilioscore.dataloader.instructionhours')
            ->getOne()
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_instructionhours',
                ['id' => $instructionHours['id']]
            ),
            json_encode(['instructionHours' => $instructionHours])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($instructionHours),
            json_decode($response->getContent(), true)['instructionHours']
        );
    }

    public function testDeleteInstructionHours()
    {
        $instructionHours = $this->container
            ->get('ilioscore.dataloader.instructionhours')
            ->getOne()
        ;

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
            $this->getUrl('get_instructionhours', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
