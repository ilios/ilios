<?php

namespace Ilios\CoreBundle\Tests\Controller;

use FOS\RestBundle\Util\Codes;

/**
 * Vocabulary controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
class VocabularyControllerTest extends AbstractControllerTest
{
    /**
     * @return array|string
     */
    protected function getFixtures()
    {
        $fixtures = parent::getFixtures();
        return array_merge($fixtures, [
            'Ilios\CoreBundle\Tests\Fixture\LoadSchoolData',
            'Ilios\CoreBundle\Tests\Fixture\LoadVocabularyData',
        ]);
    }

    /**
     * @return array|string
     */
    protected function getPrivateFields()
    {
        return [];
    }

    /**
     * @group controllers_b
     */
    public function testGetVocabulary()
    {
        $vocabulary = $this->container
            ->get('ilioscore.dataloader.vocabulary')
            ->getOne()
        ;

        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_vocabularies',
                ['id' => $vocabulary['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($vocabulary),
            json_decode($response->getContent(), true)['vocabularies'][0]
        );
    }

    /**
     * @group controllers_b
     */
    public function testGetAllVocabularies()
    {
        $this->createJsonRequest('GET', $this->getUrl('cget_vocabularies'), null, $this->getAuthenticatedUserToken());
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize(
                $this->container
                    ->get('ilioscore.dataloader.vocabulary')
                    ->getAll()
            ),
            json_decode($response->getContent(), true)['vocabularies']
        );
    }

    /**
     * @group controllers_b
     */
    public function testPostVocabulary()
    {
        $data = $this->container->get('ilioscore.dataloader.vocabulary')
            ->create();
        $postData = $data;
        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['terms']);

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_vocabularies'),
            json_encode(['vocabulary' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(Codes::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $this->assertEquals(
            $data,
            json_decode($response->getContent(), true)['vocabularies'][0],
            $response->getContent()
        );
    }

    /**
     * @group controllers
     */
    public function testPostBadVocabulary()
    {
        $invalidVocabulary = $this->container
            ->get('ilioscore.dataloader.vocabulary')
            ->createInvalid()
        ;

        $this->createJsonRequest(
            'POST',
            $this->getUrl('post_vocabularies'),
            json_encode(['vocabulary' => $invalidVocabulary]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_BAD_REQUEST, $response->getStatusCode());
    }

    /**
     * @group controllers
     */
    public function testPutVocabulary()
    {
        $data = $this->container
            ->get('ilioscore.dataloader.vocabulary')
            ->getOne();

        $postData = $data;

        //unset any parameters which should not be POSTed
        unset($postData['id']);
        unset($postData['terms']);

        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                'put_vocabularies',
                ['id' => $data['id']]
            ),
            json_encode(['vocabulary' => $postData]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_OK);
        $this->assertEquals(
            $this->mockSerialize($data),
            json_decode($response->getContent(), true)['vocabulary']
        );
    }

    /**
     * @group controllers
     */
    public function testDeleteVocabulary()
    {
        $vocabulary = $this->container
            ->get('ilioscore.dataloader.vocabulary')
            ->getOne()
        ;

        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                'delete_vocabularies',
                ['id' => $vocabulary['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Codes::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'get_vocabularies',
                ['id' => $vocabulary['id']]
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
    public function testVocabularyNotFound()
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl('get_vocabularies', ['id' => '0']),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Codes::HTTP_NOT_FOUND);
    }
}
