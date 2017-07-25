<?php

namespace Tests\IliosApiBundle;

use Doctrine\Common\Annotations\AnnotationRegistry;
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
        parent::setUp();
        $this->client = $this->makeClient();
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
        parent::tearDown();
        unset($this->client);
        unset($this->container);
        unset($this->fixtures);
        unset($this->faker);
        // Until https://github.com/doctrine/annotations/pull/135
        // is merged we need to keep the registry clean ourselves
        AnnotationRegistry::reset();
    }

    /**
     * @return array
     */
    protected function getFixtures()
    {
        return [];
    }

    /**
     * @return string
     */
    protected function getPluralName()
    {
        return strtolower($this->testName);
    }

    /**
     * @return string
     */
    protected function getSingularName()
    {
        $pluralized = $this->getPluralName();
        return Inflector::singularize($pluralized);
    }

    /**
     * @return null|string
     */
    protected function getCamelCasedPluralName()
    {
        return $this->testName;
    }

    /**
     * @return string
     */
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
     * So those endpoints which don't return all data
     * like Users::alerts[] will be able to do their comparison
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
        $name = ucfirst($this->getCamelCasedSingularName());
        $service = "Tests\\CoreBundle\\DataLoader\\{$name}Data";

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
     * @param array $files
     */
    protected function createJsonRequest($method, $url, $content = null, $token = null, $files = [])
    {
        $this->makeJsonRequest($this->client, $method, $url, $content, $token, $files);
    }

    /**
     * Test getting a single value from the API
     * @return mixed
     */
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

    /**
     * Get a single value from an API endpoint
     *
     * @param string $endpoint the name of the API endpoint
     * @param string $responseKey the key data is returned under
     * @param mixed $id the ID to fetch
     *
     * @return mixed
     */
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

    /**
     * Get getting every piece of data in the test DB
     * @return mixed
     */
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

    /**
     * Test saving new data to the API
     *
     * @param array $data
     * @param array $postData
     * @return mixed
     */
    protected function postTest(array $data, array $postData)
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $postKey = $this->getCamelCasedSingularName();
        $responseData = $this->postOne($endpoint, $postKey, $responseKey, $postData);
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

    /**
     * Test POSTing an array of similar items to the API
     * @param array $data
     * @return mixed
     */
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

    /**
     * POST a single item to the API
     *
     * @param string $endpoint to send to
     * @param string $postKey the key to send the POST under
     * @param string $responseKey the key the response will be under
     * @param array $postData the data to send
     *
     * @return mixed
     */
    protected function postOne($endpoint, $postKey, $responseKey, array $postData)
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('ilios_api_post', ['version' => 'v1', 'object' => $endpoint]),
            json_encode([$postKey => $postData]),
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_CREATED);

        return json_decode($response->getContent(), true)[$responseKey][0];
    }

    /**
     * @param string $endpoint to send to
     * @param string $responseKey the data will be returned with
     * @param array $postData to send
     *
     * @return mixed
     */
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

    /**
     * Test POSTing bad data to the API
     * @param array $data
     * @param string $error code
     */
    protected function badPostTest(array $data, $code = Response::HTTP_BAD_REQUEST)
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

        $this->assertJsonResponse($response, $code);
    }

    /**
     * When relational data is sent to the API ensure it
     * is recorded on the non-owning side of the relationship
     *
     * @param array $data to match with
     * @param array $postData to send
     * @param string $relationship the test target has to the subject
     * @param string $related the name of the related data
     * @param null $relatedName
     */
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

    /**
     * Test putting a  single value to the API
     * @param array $data
     * @param array $postData
     * @param mixed $id
     * @param bool $new if we are expecting this data to create a new item
     * @return mixed
     */
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

    /**
     * Put a single item into the API
     *
     * @param string $endpoint we are testing
     * @param string $responseKey we expect to be returned
     * @param mixed $id of the data
     * @param array $data we are changing
     * @param bool $new if this is expected to generate new data instead
     *                  of updating existing data
     * @param integer $userId
     *
     * @return mixed
     */
    protected function putOne($endpoint, $responseKey, $id, array $data, $new = false, $userId = 2)
    {
        $this->createJsonRequest(
            'PUT',
            $this->getUrl('ilios_api_put', ['version' => 'v1', 'object' => $endpoint, 'id' => $id]),
            json_encode([$responseKey => $data]),
            $this->getTokenForUser($userId)
        );
        $response = $this->client->getResponse();
        $expectedHeader = $new?Response::HTTP_CREATED:Response::HTTP_OK;
        $this->assertJsonResponse($response, $expectedHeader);

        return json_decode($response->getContent(), true)[$responseKey];
    }

    /**
     * Test deleting an object from the API
     *
     * @param $id
     */
    protected function deleteTest($id)
    {
        $endpoint = $this->getPluralName();
        $this->deleteOne($endpoint, $id);

        $this->notFoundTest($id);
    }

    /**
     * Delete an object from the API
     * @param string $endpoint we are testing
     * @param mixed $id we want to delete
     * @return null|Response
     */
    protected function deleteOne($endpoint, $id)
    {
        $this->createJsonRequest(
            'DELETE',
            $this->getUrl('ilios_api_delete', ['version' => 'v1', 'object' => $endpoint, 'id' => $id]),
            null,
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();


        $this->assertJsonResponse($response, Response::HTTP_NO_CONTENT, false);

        return $response;
    }

    /**
     * Ensure that a bad ID returns a 404
     *
     * @param $badId
     */
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

        $this->assertJsonResponse($response, Response::HTTP_NOT_FOUND);
    }

    /**
     * Test that a filter returns the expected data
     * @param array $filters we are using
     * @param array $expectedData we hope to see
     */
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
     * Get data from the API using filter parameters
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

    /**
     * Test invalid filters
     *
     * @param array $badFilters
     */
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

        $this->assertJsonResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test that updating a related entity updates the timestamp on this one
     * @param $id
     * @param $relatedEndpoint
     * @param $relatedResponseKey
     * @param $relatedData
     */
    protected function relatedTimeStampUpdateTest(
        $id,
        $relatedEndpoint,
        $relatedResponseKey,
        $relatedData
    ) {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $initialState = $this->getOne($endpoint, $responseKey, $id);
        sleep(1);
        $this->putOne($relatedEndpoint, $relatedResponseKey, $relatedData['id'], $relatedData);
        $currentState = $this->getOne($endpoint, $responseKey, $id);
        foreach ($this->getTimeStampFields() as $field) {
            $initialStamp = new DateTime($initialState[$field]);
            $currentStamp = new DateTime($currentState[$field]);

            $diff = $currentStamp->getTimestamp() - $initialStamp->getTimestamp();
            $this->assertTrue(
                $diff > 1,
                'The timestamp has increased.  Original: ' . $initialStamp->format('c') .
                ' Now: ' . $currentStamp->format('c')
            );
        }
    }

    /**
     * Test that creating related data updates a timestamp on this endpoint
     * @param $id
     * @param $relatedPluralObjectName
     * @param $relatedResponseKey
     * @param $relatedPostData
     */
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
        $this->postMany($relatedPluralObjectName, $relatedResponseKey, [$relatedPostData]);
        $currentState = $this->getOne($endpoint, $responseKey, $id);
        foreach ($this->getTimeStampFields() as $field) {
            $initialStamp = new DateTime($initialState[$field]);
            $currentStamp = new DateTime($currentState[$field]);

            $diff = $currentStamp->getTimestamp() - $initialStamp->getTimestamp();
            $this->assertTrue(
                $diff > 1,
                'The timestamp has increased.  Original: ' . $initialStamp->format('c') .
                ' Now: ' . $currentStamp->format('c')
            );
        }
    }

    /**
     * Test that deleting a related entity updates a timestamp on this one
     * @param $id
     * @param $relatedPluralObjectName
     * @param $relatedId
     */
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
                'The timestamp has increased.  Original: ' . $initialStamp->format('c') .
                ' Now: ' . $currentStamp->format('c')
            );
        }
    }
}
