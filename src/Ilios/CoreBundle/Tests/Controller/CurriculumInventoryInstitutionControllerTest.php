<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * CurriculumInventoryInstitution controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class CurriculumInventoryInstitutionControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventoryInstitutionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSchoolData'
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

    public function testGetCurriculumInventoryInstitution()
    {
        $curriculumInventoryInstitution = $this->container
            ->get('ilioscore.dataloader.curriculuminventoryinstitution')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_curriculuminventoryinstitutions',
                ['id' => $curriculumInventoryInstitution['school']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($curriculumInventoryInstitution),
            json_decode($response->getContent(), true)['curriculumInventoryInstitutions'][0]
        );
    }

    public function testGetAllCurriculumInventoryInstitutions()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_curriculuminventoryinstitutions'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.curriculuminventoryinstitution')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['curriculumInventoryInstitutions']
        );
    }

    public function testPostCurriculumInventoryInstitution()
    {
        $data = $this->container->get('ilioscore.dataloader.curriculuminventoryinstitution')
            ->create();

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventoryinstitutions'),
            json_encode(['curriculumInventoryInstitution' => $postData])
        );

        $response = $this->client->getResponse();
        $headers  = [];

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['curriculumInventoryInstitutions'][0],
            $response->getContent()
        );
    }

    public function testPostBadCurriculumInventoryInstitution()
    {
        $invalidCurriculumInventoryInstitution = $this->container
            ->get('ilioscore.dataloader.curriculuminventoryinstitution')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventoryinstitutions'),
            json_encode(['curriculumInventoryInstitution' => $invalidCurriculumInventoryInstitution])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testPutCurriculumInventoryInstitution()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.curriculuminventoryinstitution')
            ->getOne();

        $postData = $data;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_curriculuminventoryinstitutions',
                ['school' => $data['school']]
            ),
            json_encode(['curriculumInventoryInstitution' => $postData])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['curriculumInventoryInstitution']
        );
    }

    public function testDeleteCurriculumInventoryInstitution()
    {
        $curriculumInventoryInstitution = $this->container
            ->get('ilioscore.dataloader.curriculuminventoryinstitution')
            ->getOne()
        ;

        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_curriculuminventoryinstitutions',
                ['id' => $curriculumInventoryInstitution['school']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_curriculuminventoryinstitutions',
                ['id' => $curriculumInventoryInstitution['school']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testCurriculumInventoryInstitutionNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_curriculuminventoryinstitutions', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
