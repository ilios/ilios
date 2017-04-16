<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * CurriculumInventoryExport controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class CurriculumInventoryExportControllerTest extends AbstractControllerTest
{
    /**
     * @return array
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventoryExportData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventoryReportData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData'
        ];
    }

    public function testGetCurriculumInventoryExport()
    {
        $curriculumInventoryExport = $this->container
            ->get('ilioscore.dataloader.curriculuminventoryexport')
            ->getOne()['curriculumInventoryExport']
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_curriculuminventoryexports',
                ['id' => $curriculumInventoryExport['report']]
            )
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $curriculumInventoryExport,
            json_decode($response->getContent(), true)['curriculumInventoryExport']
        );
    }

    public function testGetAllCurriculumInventoryExports()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_curriculuminventoryexports'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.curriculuminventoryexport')->getAll(),
            json_decode($response->getContent(), true)
        );
    }

    public function testPostCurriculumInventoryExport()
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventoryexports'),
            json_encode(
                $this->container->get('ilioscore.dataloader.curriculuminventoryexport')
                    ->create()['curriculumInventoryExport']
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_CREATED);
        $this->assertArrayHasKey('location', $response->headers->all(), print_r($response->headers->all(), true));
    }

    public function testPostBadCurriculumInventoryExport()
    {
        $invalidCurriculumInventoryExport = array_shift(
            $this->container->get('ilioscore.dataloader.curriculuminventoryexport')->invalid()
        );

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventoryexports'),
            $invalidCurriculumInventoryExport
        );

        $response = $this->client->getResponse();
        $this->assertEquals($response->getStatusCode(), Codes::HTTP_BAD_REQUEST);
    }

    public function testPutCurriculumInventoryExport()
    {
        $curriculumInventoryExport = $this->container
            ->get('ilioscore.dataloader.curriculuminventoryexport')
            ->createWithId()['curriculumInventoryExport']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_curriculuminventoryexports',
                ['id' => $curriculumInventoryExport['report']]
            ),
            json_encode($curriculumInventoryExport)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->assertEquals(
            $this->container->get('ilioscore.dataloader.curriculuminventoryexport')
                ->getLastCreated()['curriculumInventoryExport'],
            json_decode($response->getContent(), true)['curriculumInventoryExport']
        );
    }

    public function testDeleteCurriculumInventoryExport()
    {
        $curriculumInventoryExport = $this->container
            ->get('ilioscore.dataloader.curriculuminventoryexport')
            ->createWithId()['curriculumInventoryExport']
        ;

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_curriculuminventoryexports',
                ['id' => $curriculumInventoryExport['report']]
            ),
            json_encode($curriculumInventoryExport)
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_CREATED);
        $this->client->request(
            'DELETE',
            $this->getUrl(
                'delete_curriculuminventoryexports',
                ['id' => $curriculumInventoryExport['report']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->client->request(
            'GET',
            $this->getUrl(
                'get_curriculuminventoryexports',
                ['id' => $curriculumInventoryExport['report']]
            )
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    public function testCurriculumInventoryExportNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_curriculuminventoryexports', ['id' => '-9999'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
