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
     * @return array|string
     */
    protected function getFixtures()
    {
        return [
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventoryExportData',
            'Ilios\CoreBundle\Tests\Fixture\LoadCurriculumInventoryReportData',
            'Ilios\CoreBundle\Tests\Fixture\LoadUserData'
        ];
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [
            'createdAt'
        ];
    }

    public function testGetCurriculumInventoryExport()
    {
        $curriculumInventoryExport = $this->container
            ->get('ilioscore.dataloader.curriculuminventoryexport')
            ->getOne()
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
            $this->mockSerialize($curriculumInventoryExport),
            json_decode($response->getContent(), true)['curriculumInventoryExports'][0]
        );
    }

    public function testGetAllCurriculumInventoryExports()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_curriculuminventoryexports'));
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.curriculuminventoryexport')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['curriculumInventoryExports']
        );
    }

    public function testPostCurriculumInventoryExport()
    {
        $data = $this->container->get('ilioscore.dataloader.curriculuminventoryexport')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['document']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventoryexports'),
            json_encode(['curriculumInventoryExport' => $postData])
        );

        $response = $this->client->getResponse();
        $headers  = [];

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['curriculumInventoryExports'][0],
            $response->getContent()
        );
    }

    public function testPostBadCurriculumInventoryExport()
    {
        $invalidCurriculumInventoryExport = $this->container
            ->get('ilioscore.dataloader.curriculuminventoryexport')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_curriculuminventoryexports'),
            json_encode(['curriculumInventoryExport' => $invalidCurriculumInventoryExport])
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    public function testPutCurriculumInventoryExport()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.curriculuminventoryexport')
            ->getOne();

        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['document']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_curriculuminventoryexports',
                ['id' => $data['id']]
            ),
            json_encode(['curriculumInventoryExport' => $postData])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['curriculumInventoryExport']
        );
    }

    public function testDeleteCurriculumInventoryExport()
    {
        $curriculumInventoryExport = $this->container
            ->get('ilioscore.dataloader.curriculuminventoryexport')
            ->getOne()
        ;

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
            $this->getUrl('get_curriculuminventoryexports', ['id' => '0'])
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
