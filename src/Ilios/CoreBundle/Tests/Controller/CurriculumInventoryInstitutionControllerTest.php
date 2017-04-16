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
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventoryInstitutionData',
            'Ilios\CoreBundle\Tests\Fixture\LoadSchoolData'
        ];
    }

    public function testGetCurriculumInventoryInstitution()
    {
        $curriculumInventoryInstitution = $this->container
            ->get('ilioscore.dataloader.curriculuminventoryinstitution')
            ->getOne()['curriculumInventoryInstitution']
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
            $curriculumInventoryInstitution,
            json_decode($response->getContent(), true)['curriculumInventoryInstitution']
        );
    }

    public function testGetAllCurriculumInventoryInstitutions()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_curriculuminventoryinstitutions'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.curriculuminventoryinstitution')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostCurriculumInventoryInstitution()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventoryinstitutions'),
            json_encode(
                $this->container->get('ilioscore.dataloader.curriculuminventoryinstitution')
                    ->create()['curriculumInventoryInstitution']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadCurriculumInventoryInstitution()
    {
        $invalidCurriculumInventoryInstitution = array_shift(
            $this->container->get('ilioscore.dataloader.curriculuminventoryinstitution')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventoryinstitutions'),
            $invalidCurriculumInventoryInstitution
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutCurriculumInventoryInstitution()
    {
        $curriculumInventoryInstitution = $this->container
            ->get('ilioscore.dataloader.curriculuminventoryinstitution')
            ->createWithId()['curriculumInventoryInstitution']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_curriculuminventoryinstitutions',
                ['id' => $curriculumInventoryInstitution['school']]
            ),
            json_encode($curriculumInventoryInstitution)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.curriculuminventoryinstitution')
                ->getLastCreated()['curriculumInventoryInstitution'],
            json_decode($response->getContent(), true)['curriculumInventoryInstitution']
        );
    }

    public function testDeleteCurriculumInventoryInstitution()
    {
        $curriculumInventoryInstitution = $this->container
            ->get('ilioscore.dataloader.curriculuminventoryinstitution')
            ->createWithId()['curriculumInventoryInstitution']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_curriculuminventoryinstitutions',
                ['id' => $curriculumInventoryInstitution['school']]
            ),
            json_encode($curriculumInventoryInstitution)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
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
            $this->getUrl('get_curriculuminventoryinstitutions', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
