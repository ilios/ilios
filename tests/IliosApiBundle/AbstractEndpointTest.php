<?php

namespace Tests\IliosApiBundle;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use DateTime;
use Doctrine\Common\DataFixtures\ProxyReferenceRepository;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Tests\CoreBundle\DataLoader\DataLoaderInterface;
use Tests\CoreBundle\Traits\JsonControllerTest;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Common\Util\Inflector;
use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;

/**
 * Abstract Testing glue for endpoints
 * @package Tests\IliosApiBundle
 */
abstract class AbstractEndpointTest extends WebTestCase
{
    use JsonControllerTest;

    /**
     * @var string|null the name of this endpoint (plural)
     */
    protected $testName = null;


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
     * @var FakerGenerator
     */
    protected $faker;


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
        unset($this->faker);
    }

    /**
     * @return array
     */
    protected function getFixtures()
    {
        return [];
    }

    protected function getPluralName()
    {
        return strtolower($this->testName);
    }

    protected function getSingularName()
    {
        $pluralized = $this->getPluralName();
        return Inflector::singularize($pluralized);
    }

    protected function getCamelCasedPluralName()
    {
        return $this->testName;
    }

    protected function getCamelCasedSingularName()
    {
        $pluralized = $this->getCamelCasedPluralName();
        return Inflector::singularize($pluralized);
    }

    /**
     * @return FakerGenerator
     */
    protected function getFaker()
    {
        if (!$this->faker) {
            $this->faker = FakerFactory::create();
            $this->faker->seed(17105);
        }

        return $this->faker;
    }

    /**
     * An overridable way to do the field comparison
     * So those endpoints which dont return all data
     * like Users::alerts[] will be able to do thier comparison
     *
     * @param array $expected
     * @param array $result
     */
    protected function compareData(array $expected, array $result)
    {
        $this->assertEquals(
            $expected,
            $result
        );
    }

    /**
     * @return DataLoaderInterface
     */
    protected function getDataLoader()
    {
        $name = $this->getSingularName();
        $service = "ilioscore.dataloader.{$name}";

        /** @var DataLoaderInterface $dataLoader */
        $dataLoader = $this->container->get($service);
        return $dataLoader;
    }

    /**
     * @return array
     */
    protected function getTimeStampFields()
    {
        return [];
    }

    /**
     * Create a JSON request
     *
     * @param string $method
     * @param string $url
     * @param string $content
     * @param string $token
     */
    protected function createJsonRequest($method, $url, $content = null, $token = null, $files = array())
    {
        $this->makeJsonRequest($this->client, $method, $url, $content, $token, $files);
    }

    protected function getOneTest()
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $loader = $this->getDataLoader();
        $data = $loader->getOne();
        $returnedData = $this->getOne($endpoint, $responseKey, $data['id']);

        foreach ($this->getTimeStampFields() as $field) {
            $stamp = new DateTime($returnedData[$field]);
            unset($returnedData[$field]);
            $now = new DateTime();
            $diff = $now->diff($stamp);
            $this->assertTrue($diff->y < 1, "The {$field} timestamp is within the last year");
        }
        $this->compareData($data, $returnedData);

        return $returnedData;
    }

    protected function getOne($endpoint, $responseKey, $id)
    {
        $url = $this->getUrl(
            'ilios_api_get',
            ['version' => 'v1', 'object' => $endpoint, 'id' => $id]
        );
        $this->createJsonRequest(
            'GET',
            $url,
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        if (Response::HTTP_NOT_FOUND === $response->getStatusCode()) {
            $this->fail("Unable to load url: {$url}");
        }

        $this->assertJsonResponse($response, Response::HTTP_OK);
        return json_decode($response->getContent(), true)[$responseKey][0];
    }

    protected function getAllTest()
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $loader = $this->getDataLoader();
        $data = $loader->getAll();
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'ilios_api_getall',
                ['version' => 'v1', 'object' => $endpoint]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_OK);
        $responses = json_decode($response->getContent(), true)[$responseKey];

        $now = new DateTime();
        foreach ($responses as $i => $response) {
            foreach ($this->getTimeStampFields() as $field) {
                $stamp = new DateTime($response[$field]);
                unset($response[$field]);
                $diff = $now->diff($stamp);
                $this->assertTrue($diff->y < 1, "The {$field} timestamp is within the last year");
            }
            $this->compareData($data[$i], $response);
        }

        return $responses;
    }

    protected function postTest(array $data, array $postData)
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $responseData = $this->postOne($endpoint, $responseKey, $postData);
        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getOne($endpoint, $responseKey, $responseData['id']);

        $now = new DateTime();
        foreach ($this->getTimeStampFields() as $field) {
            $stamp = new DateTime($fetchedResponseData[$field]);
            unset($fetchedResponseData[$field]);
            $diff = $now->diff($stamp);
            $this->assertTrue($diff->y < 1, "The {$field} timestamp is within the last year");
        }

        $this->compareData($data, $fetchedResponseData);

        return $fetchedResponseData;
    }

    protected function postManyTest(array $data)
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $responseData = $this->postMany($endpoint, $responseKey, $data);
        $ids = array_map(function (array $arr) {
            return $arr['id'];
        }, $responseData);
        $filters = [
            'filters[id]' => $ids,
            'limit' => count($ids)
        ];
        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getFiltered($endpoint, $responseKey, $filters);

        usort($fetchedResponseData, function ($a, $b) {
            return strnatcasecmp($a['id'], $b['id']);
        });

        $now = new DateTime();
        foreach ($data as $i => $datum) {
            $response = $fetchedResponseData[$i];
            foreach ($this->getTimeStampFields() as $field) {
                $stamp = new DateTime($response[$field]);
                unset($response[$field]);
                $diff = $now->diff($stamp);
                $this->assertTrue($diff->y < 1, "The {$field} timestamp is within the last year");
            }

            $this->compareData($datum, $response);
        }

        return $fetchedResponseData;
    }

    protected function postOne($endpoint, $responseKey, array $postData)
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('ilios_api_post', ['version' => 'v1', 'object' => $endpoint]),
            json_encode([$responseKey => [$postData]]),
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_CREATED);

        return json_decode($response->getContent(), true)[$responseKey][0];
    }

    protected function postMany($endpoint, $responseKey, array $postData)
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('ilios_api_post', ['version' => 'v1', 'object' => $endpoint]),
            json_encode([$responseKey => $postData]),
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_CREATED);

        return json_decode($response->getContent(), true)[$responseKey];
    }

    protected function badPostTest(array $data)
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('ilios_api_post', ['version' => 'v1', 'object' => $endpoint]),
            json_encode([$responseKey => [$data]]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(
            Response::HTTP_BAD_REQUEST,
            $response->getStatusCode(),
            'Wrong Response Header.  Page Body: ' . substr($response->getContent(), 0, 200)
        );
    }

    public function relatedPostDataTest(array $data, array $postData, $relationship, $related, $relatedName = null)
    {
        $responseData = $this->postTest($data, $postData);

        $newId = $responseData['id'];
        $relatedName = null == $relatedName?$related:$relatedName;
        $this->assertArrayHasKey($relatedName, $postData, 'Missing related key: ' . var_export($postData, true));
        foreach ($postData[$relatedName] as $id) {
            $obj = $this->getOne(strtolower($related), $related, $id);
            $this->assertTrue(array_key_exists($relationship, $obj), var_export($obj, true));
            $this->assertTrue(in_array($newId, $obj[$relationship]));
        }
    }

    protected function putTest(array $data, array $postData, $id, $new = false)
    {
        $endpoint = $this->getPluralName();
        $putResponseKey = $this->getCamelCasedSingularName();
        $getResponseKey = $this->getCamelCasedPluralName();
        $responseData = $this->putOne($endpoint, $putResponseKey, $id, $postData, $new);
        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getOne($endpoint, $getResponseKey, $responseData['id']);

        $now = new DateTime();
        foreach ($this->getTimeStampFields() as $field) {
            $stamp = new DateTime($fetchedResponseData[$field]);
            unset($fetchedResponseData[$field]);
            $diff = $now->diff($stamp);
            $this->assertTrue($diff->y < 1, "The {$field} timestamp is within the last year");
        }

        $this->compareData($data, $fetchedResponseData);

        return $fetchedResponseData;
    }

    protected function putOne($endpoint, $responseKey, $id, array $data, $new = false)
    {
        $this->createJsonRequest(
            'PUT',
            $this->getUrl('ilios_api_put', ['version' => 'v1', 'object' => $endpoint, 'id' => $id]),
            json_encode([$responseKey => $data]),
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $expectedHeader = $new?Response::HTTP_CREATED:Response::HTTP_OK;
        $this->assertJsonResponse($response, $expectedHeader);

        return json_decode($response->getContent(), true)[$responseKey];
    }

    protected function deleteTest($id)
    {
        $endpoint = $this->getPluralName();
        $this->deleteOne($endpoint, $id);

        $this->notFoundTest($id);
    }

    protected function deleteOne($endpoint, $id)
    {
        $this->createJsonRequest(
            'DELETE',
            $this->getUrl('ilios_api_delete', ['version' => 'v1', 'object' => $endpoint, 'id' => $id]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();


        $this->assertEquals(
            Response::HTTP_NO_CONTENT,
            $response->getStatusCode(),
            'Wrong Response Header.  Page Body: ' . substr($response->getContent(), 0, 200)
        );

        return $response;
    }

    protected function notFoundTest($badId)
    {
        $endpoint = $this->getPluralName();
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                'ilios_api_get',
                ['version' => 'v1', 'object' => $endpoint, 'id' => $badId]
            ),
            null,
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(
            Response::HTTP_NOT_FOUND,
            $response->getStatusCode(),
            'Wrong Response Header.  Page Body: ' . substr($response->getContent(), 0, 200)
        );
    }

    protected function filterTest(array $filters, array $expectedData)
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $filteredData = $this->getFiltered($endpoint, $responseKey, $filters);

        $timeStampFields = $this->getTimeStampFields();
        $responseData = array_map(function ($arr) use ($timeStampFields) {
            foreach ($this->getTimeStampFields() as $field) {
                unset($arr[$field]);
            }
            return $arr;
        }, $filteredData);

        $this->assertEquals(
            count($expectedData),
            count($responseData),
            'Wrong Number of responses returned from filter got: ' . var_export($responseData, true)
        );
        foreach ($expectedData as $i => $data) {
            $this->compareData($data, $responseData[$i]);
        }
    }

    /**
     * @param $endpoint
     * @param $responseKey
     * @param array $filters
     * @return mixed
     */
    protected function getFiltered($endpoint, $responseKey, array $filters)
    {
        $parameters = array_merge([
            'version' => 'v1',
            'object' => $endpoint
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

        return json_decode($response->getContent(), true)[$responseKey];
    }

    protected function badFilterTest(array $badFilters)
    {
        $endpoint = $this->getPluralName();
        $parameters = array_merge([
            'version' => 'v1',
            'object' => $endpoint
        ], $badFilters);
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

        $this->assertEquals(
            Response::HTTP_INTERNAL_SERVER_ERROR,
            $response->getStatusCode(),
            'Wrong Response Header.  Page Body: ' . substr($response->getContent(), 0, 200)
        );
    }

    protected function relatedTimeStampUpdateTest(
        $id,
        $relatedPluralObjectName,
        $relatedResponseKey,
        $relatedData
    ) {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $initialState = $this->getOne($endpoint, $responseKey, $id);
        sleep(1);
        $this->putOne($relatedPluralObjectName, $relatedResponseKey, $relatedData['id'], $relatedData);
        $currentState = $this->getOne($endpoint, $responseKey, $id);
        foreach ($this->getTimeStampFields() as $field) {
            $initialStamp = new DateTime($initialState[$field]);
            $currentStamp = new DateTime($currentState[$field]);

            $diff = $currentStamp->getTimestamp() - $initialStamp->getTimestamp();
            $this->assertTrue(
                $diff > 1,
                'The updatedAt timestamp has increased.  Original: ' . $initialStamp->format('c') .
                ' Now: ' . $currentStamp->format('c')
            );
        }
    }

    protected function relatedTimeStampPostTest(
        $id,
        $relatedPluralObjectName,
        $relatedResponseKey,
        $relatedPostData
    ) {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $initialState = $this->getOne($endpoint, $responseKey, $id);
        sleep(1);
        $this->postOne($relatedPluralObjectName, $relatedResponseKey, $relatedPostData);
        $currentState = $this->getOne($endpoint, $responseKey, $id);
        foreach ($this->getTimeStampFields() as $field) {
            $initialStamp = new DateTime($initialState[$field]);
            $currentStamp = new DateTime($currentState[$field]);

            $diff = $currentStamp->getTimestamp() - $initialStamp->getTimestamp();
            $this->assertTrue(
                $diff > 1,
                'The updatedAt timestamp has increased.  Original: ' . $initialStamp->format('c') .
                ' Now: ' . $currentStamp->format('c')
            );
        }
    }

    protected function relatedTimeStampDeleteTest(
        $id,
        $relatedPluralObjectName,
        $relatedId
    ) {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $initialState = $this->getOne($endpoint, $responseKey, $id);
        sleep(1);
        $this->deleteOne($relatedPluralObjectName, $relatedId);
        $currentState = $this->getOne($endpoint, $responseKey, $id);
        foreach ($this->getTimeStampFields() as $field) {
            $initialStamp = new DateTime($initialState[$field]);
            $currentStamp = new DateTime($currentState[$field]);

            $diff = $currentStamp->getTimestamp() - $initialStamp->getTimestamp();
            $this->assertTrue(
                $diff > 1,
                'The updatedAt timestamp has increased.  Original: ' . $initialStamp->format('c') .
                ' Now: ' . $currentStamp->format('c')
            );
        }
    }
}
