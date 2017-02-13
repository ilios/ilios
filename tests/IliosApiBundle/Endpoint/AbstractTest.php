<?php

namespace Tests\IliosApiBundle\Endpoint;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use DateTime;
use Doctrine\Common\DataFixtures\ProxyReferenceRepository;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tests\CoreBundle\DataLoader\DataLoaderInterface;
use Tests\CoreBundle\Traits\JsonControllerTest;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Util\Inflector;

/**
 * Session controller Test.
 * @package Ilios\CoreBundle\Test\Controller;
 */
abstract class AbstractTest extends WebTestCase
{
    use JsonControllerTest;
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var ProxyReferenceRepository
     */
    protected $fixtures;

    /**
     * @return array
     */
    protected function getFixtures()
    {
        return [];
    }

    /**
     * @return DataLoaderInterface
     */
    abstract protected function getDataLoader();

    public function setUp()
    {
        $this->client = static::createClient();
        $this->client->followRedirects();
        $this->container = $this->client->getContainer();

        $authFixtures = [
            'Tests\CoreBundle\Fixture\LoadAuthenticationData',
            'Tests\CoreBundle\Fixture\LoadPermissionData',
        ];
        $testFixtures = $this->getFixtures();
        $fixtures = array_merge($authFixtures, $testFixtures);
        $this->fixtures = $this->loadFixtures($fixtures)->getReferenceRepository();
    }

    public function tearDown()
    {
        unset($this->client);
        unset($this->container);
        unset($this->fixtures);
    }

    /**
     * Create a JSON request
     *
     * @param string $method
     * @param string $url
     * @param string $content
     * @param string $token
     */
    public function createJsonRequest($method, $url, $content = null, $token = null, $files = array())
    {
        $this->makeJsonRequest($this->client, $method, $url, $content, $token, $files);
    }

    protected function getOneTest($pluralObjectName, $timeStampFields = [])
    {
        $loader = $this->getDataLoader();
        $data = $loader->getOne();
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'ilios_api_get',
                ['version' => 'v1', 'object' => $pluralObjectName, 'id' => $data['id']]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_OK);
        $returnedData = json_decode($response->getContent(), true)[$pluralObjectName][0];

        foreach ($timeStampFields as $field) {
            $stamp = new DateTime($returnedData[$field]);
            unset($returnedData[$field]);
            $now = new DateTime();
            $diff = $now->diff($stamp);
            $this->assertTrue($diff->y < 1, "The {$field} timestamp is within the last year");
        }
        $this->assertEquals(
            $data,
            $returnedData
        );

    }

    public function getAllTest($pluralObjectName, $timeStampFields = [])
    {
        $loader = $this->getDataLoader();
        $data = $loader->getAll();
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'ilios_api_getall',
                ['version' => 'v1', 'object' => $pluralObjectName]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_OK);
        $responses = json_decode($response->getContent(), true)[$pluralObjectName];

        $now = new DateTime();
        foreach ($responses as $i => $response) {
            foreach ($timeStampFields as $field) {
                $stamp = new DateTime($response[$field]);
                unset($response[$field]);
                $diff = $now->diff($stamp);
                $this->assertTrue($diff->y < 1, "The {$field} timestamp is within the last year");
            }
            $this->assertEquals(
                $data[$i],
                $response
            );
        }

    }

    public function postTest($pluralObjectName, $data, $postData, $timeStampFields = [])
    {
        $singularObjectName = Inflector::singularize($pluralObjectName);
        $this->createJsonRequest(
            'POST',
            $this->getUrl('ilios_api_post', ['version' => 'v1', 'object' => $pluralObjectName]),
            json_encode([$singularObjectName => $postData]),
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode(), $response->getContent());
        $responseData = json_decode($response->getContent(), true)[$pluralObjectName][0];

        $now = new DateTime();
        foreach ($timeStampFields as $field) {
            $stamp = new DateTime($responseData[$field]);
            unset($responseData[$field]);
            $diff = $now->diff($stamp);
            $this->assertTrue($diff->y < 1, "The {$field} timestamp is within the last year");
        }

        $this->assertEquals(
            $data,
            $responseData
        );
    }

    public function badPostTest($pluralObjectName, $data, $timeStampFields = [])
    {
        $singularObjectName = Inflector::singularize($pluralObjectName);
        $this->createJsonRequest(
            'POST',
            $this->getUrl('ilios_api_post', ['version' => 'v1', 'object' => $pluralObjectName]),
            json_encode([$singularObjectName => $data]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode(), $response->getContent());
    }

    public function putTest($pluralObjectName, $data, $postData, $timeStampFields = [])
    {
        $singularObjectName = Inflector::singularize($pluralObjectName);
        $this->createJsonRequest(
            'PUT',
            $this->getUrl('ilios_api_put', ['version' => 'v1', 'object' => $pluralObjectName, 'id' => $data['id']]),
            json_encode([$singularObjectName => $postData]),
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode(), $response->getContent());
        $responseData = json_decode($response->getContent(), true)[$singularObjectName];

        $now = new DateTime();
        foreach ($timeStampFields as $field) {
            $stamp = new DateTime($responseData[$field]);
            unset($responseData[$field]);
            $diff = $now->diff($stamp);
            $this->assertTrue($diff->y < 1, "The {$field} timestamp is within the last year");
        }

        $this->assertEquals(
            $data,
            $responseData
        );
    }

    public function deleteTest($pluralObjectName, $id)
    {
        $this->createJsonRequest(
            'DELETE',
            $this->getUrl('ilios_api_delete', ['version' => 'v1', 'object' => $pluralObjectName, 'id' => $id]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'ilios_api_get',
                ['version' => 'v1', 'object' => $pluralObjectName, 'id' => $id]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();
        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
    }

    protected function notFoundTest($pluralObjectName, $badId)
    {
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'ilios_api_get',
                ['version' => 'v1', 'object' => $pluralObjectName, 'id' => $badId]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_NOT_FOUND);
    }

    public function filterTest($pluralObjectName, array $filters, $expectedData, array $timeStampFields = [])
    {
        $parameters = array_merge([
            'version' => 'v1',
            'object' => $pluralObjectName
        ], $filters);
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'ilios_api_getall',
                $parameters
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_OK);
        $responseData = array_map(function ($arr) use ($timeStampFields) {
            foreach ($timeStampFields as $field) {
                unset($arr[$field]);
            }
            return $arr;
        }, json_decode($response->getContent(), true)[$pluralObjectName]);

        $this->assertEquals(count($expectedData), count($responseData), var_export($responseData, true));
        foreach ($expectedData as $i => $data) {
            $this->assertEquals(
                $data,
                $responseData[$i]
            );
        }
    }
}
