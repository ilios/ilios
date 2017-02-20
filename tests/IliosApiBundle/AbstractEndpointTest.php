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
 * Session controller Test.
 * @package Tests\IliosApiBundle\Endpoints
 */
abstract class AbstractEndpointTest extends WebTestCase
{
    use JsonControllerTest;

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

    /**
     * @return array
     */
    protected function getFixtures()
    {
        return [];
    }

    /**
     * @return array [[positions], [[filterKey, filterValue]]
     * the key for each item is reflected in the failure message
     * positions:  array of the positions the expected items from the DataLoader
     * filter: array containing the filterKey and filterValue we are testing
     */
    public abstract function filtersToTest();

    /**
     * @return array [field, value]
     * field / value pairs to modify
     * field: readonly property name on the entity
     * value: something to set it to
     * the key for each item is reflected in the failure message
     * each one will be separately tested in a PUT request
     */
    public abstract function putsToTest();

    /**
     * @return array [field, value, id]
     *
     * field / value / id sets that are readOnly
     * field: readonly property name on the entity
     * value: something to set it to
     * id: the ID of the object we want to test.  The has to be provided seperatly
     * because we can't extract it from the $data without invalidting this test
     *
     * the key for each item is reflected in the failure message
     * each one will be separately tested in a PUT request
     */
    public abstract function readOnliesToTest();

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

    public function testGetOne()
    {
        $this->getOneTest();
    }

    public function testGetAll()
    {
        $this->getAllTest();
    }

    public function testPost()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->create();
        $postData = $data;
        $this->postTest($data, $postData);
    }

    public function testPostBad()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->createInvalid();
        $this->badPostTest($data);
    }

    public function testPostMany()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->createMany(51);
        $this->postManyTest($data);
    }

    /**
     * @dataProvider putsToTest
     */
    public function testPut($key, $value)
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        if (array_key_exists($key, $data) and $data[$key] == $value) {
            $this->fail(
                "This value is already set for {$key}. " .
                "Modify " . get_class($this) . '::putsToTest'
            );
        }
        $data[$key] = $value;

        $postData = $data;
        $this->putTest($data, $postData, $data['id']);
    }

    public function testPutForAllData()
    {
        $putsToTest = $this->putsToTest();
        $firstPut = array_shift($putsToTest);
        $changeKey = $firstPut[0];
        $changeValue = $firstPut[1];
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        foreach ($all as $data) {
            $data[$changeKey] = $changeValue;

            $this->putTest($data, $data, $data['id']);
        }
    }

    /**
     * @dataProvider readOnliesToTest
     */
    public function testPutReadOnly($key = null, $id = null, $value = null)
    {
        if (
            null != $key &&
            null != $id &&
            null != $value
        ) {
            $dataLoader = $this->getDataLoader();
            $data = $dataLoader->getOne();
            if (array_key_exists($key, $data) and $data[$key] == $value) {
                $this->fail(
                    "This value is already set for {$key}. " .
                    "Modify " . get_class($this) . '::readOnliesToTest'
                );
            }
            $postData = $data;
            $postData[$key] = $value;

            //nothing should change
            $this->putTest($data, $postData, $id);
        }
    }

    public function testDelete()
    {
        $dataLoader = $this->getDataLoader();
        $data = $dataLoader->getOne();
        $this->deleteTest($data['id']);
    }

    public function testNotFound()
    {
        $this->notFoundTest(99);
    }

    /**
     * @dataProvider filtersToTest
     */
    public function testFilters(array $dataKeys = [], array $filterParts = [])
    {
        if (empty($filterParts)) {
            $this->markTestSkipped('Missing filters tests for this endpoint');
            return;
        }
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $expectedData = array_map(function($i) use ($all) {
            return $all[$i];
        }, $dataKeys);
        $filters = [];
        foreach ($filterParts as $key => $value) {
            $filters["filters[{$key}]"] = $value;
        }
        $this->filterTest($filters, $expectedData);
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
        $pluralObjectName = $this->getPluralName();
        $loader = $this->getDataLoader();
        $data = $loader->getOne();
        $returnedData = $this->getOne($pluralObjectName, $data['id']);

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

    protected function getOne($pluralObjectName, $id)
    {
        $url = $this->getUrl(
            'ilios_api_get',
            ['version' => 'v1', 'object' => $pluralObjectName, 'id' => $id]
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
        return json_decode($response->getContent(), true)[$pluralObjectName][0];
    }

    protected function getAllTest()
    {
        $pluralObjectName = $this->getPluralName();
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

    protected function postTest($data, $postData)
    {
        $pluralObjectName = $this->getPluralName();
        $responseData = $this->postOne($pluralObjectName, $postData);
        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getOne($pluralObjectName, $responseData['id']);

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

    protected function postManyTest($data)
    {
        $pluralObjectName = $this->getPluralName();
        $responseData = $this->postMany($pluralObjectName, $data);
        $ids = array_map(function (array $arr) {
            return $arr['id'];
        }, $responseData);
        $filters = [
            'filters[id]' => $ids,
            'limit' => count($ids)
        ];
        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getFiltered($pluralObjectName, $filters);

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

    protected function postOne($pluralObjectName, $postData)
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('ilios_api_post', ['version' => 'v1', 'object' => $pluralObjectName]),
            json_encode([$pluralObjectName => $postData]),
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_CREATED);

        return json_decode($response->getContent(), true)[$pluralObjectName][0];
    }

    protected function postMany($pluralObjectName, $postData)
    {
        $this->createJsonRequest(
            'POST',
            $this->getUrl('ilios_api_post', ['version' => 'v1', 'object' => $pluralObjectName]),
            json_encode([$pluralObjectName => $postData]),
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_CREATED);

        return json_decode($response->getContent(), true)[$pluralObjectName];
    }

    protected function badPostTest($data)
    {
        $pluralObjectName = $this->getPluralName();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('ilios_api_post', ['version' => 'v1', 'object' => $pluralObjectName]),
            json_encode([$pluralObjectName => $data]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(
            Response::HTTP_BAD_REQUEST,
            $response->getStatusCode(),
            'Wrong Response Header.  Page Body: ' . substr($response->getContent(), 0, 200)
        );
    }

    public function relatedPostDataTest($data, $postData, $relationship, $related, $relatedName = null)
    {
        $responseData = $this->postTest($data, $postData);

        $newId = $responseData['id'];
        $relatedName = null == $relatedName?$related:$relatedName;
        $this->assertArrayHasKey($relatedName, $postData, 'Missing related key: ' . var_export($postData, true));
        foreach ($postData[$relatedName] as $id) {
            $obj = $this->getOne($related, $id);
            $this->assertTrue(array_key_exists($relationship, $obj), var_export($obj, true));
            $this->assertTrue(in_array($newId, $obj[$relationship]));
        }
    }

    protected function putTest($data, $postData, $id)
    {
        $pluralObjectName = $this->getPluralName();
        $responseData = $this->putOne($pluralObjectName, $id, $postData);
        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getOne($pluralObjectName, $responseData['id']);

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

    protected function putOne($pluralObjectName, $id, $data)
    {
        $singularObjectName = Inflector::singularize($pluralObjectName);
        $this->createJsonRequest(
            'PUT',
            $this->getUrl('ilios_api_put', ['version' => 'v1', 'object' => $pluralObjectName, 'id' => $id]),
            json_encode([$singularObjectName => $data]),
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_OK);

        return json_decode($response->getContent(), true)[$singularObjectName];
    }

    protected function deleteTest($id)
    {
        $pluralObjectName = $this->getPluralName();
        $this->deleteOne($pluralObjectName, $id);

        $this->notFoundTest($id);
    }

    protected function deleteOne($pluralObjectName, $id)
    {
        $this->createJsonRequest(
            'DELETE',
            $this->getUrl('ilios_api_delete', ['version' => 'v1', 'object' => $pluralObjectName, 'id' => $id]),
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
        $pluralObjectName = $this->getPluralName();
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

        $this->assertEquals(
            Response::HTTP_NOT_FOUND,
            $response->getStatusCode(),
            'Wrong Response Header.  Page Body: ' . substr($response->getContent(), 0, 200)
        );
    }

    protected function filterTest(array $filters, array $expectedData)
    {
        $pluralObjectName = $this->getPluralName();
        $filteredData = $this->getFiltered($pluralObjectName, $filters);

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
            'Wrong Number of responses returned from filter got: ' . var_export($responseData, true));
        foreach ($expectedData as $i => $data) {
            $this->compareData($data, $responseData[$i]);
        }
    }

    /**
     * @param $pluralObjectName
     * @param array $filters
     * @return mixed
     */
    protected function getFiltered($pluralObjectName, array $filters)
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

        return json_decode($response->getContent(), true)[$pluralObjectName];
    }

    protected function badFilterTest(array $badFilters)
    {
        $pluralObjectName = $this->getPluralName();
        $parameters = array_merge([
            'version' => 'v1',
            'object' => $pluralObjectName
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
        array $timeStampFields,
        $relatedPluralObjectName,
        $relatedData

    ){
        $pluralObjectName = $this->getPluralName();
        $initialState = $this->getOne($pluralObjectName, $id);
        sleep(1);
        $this->putOne($relatedPluralObjectName, $relatedData['id'], $relatedData);
        $currentState = $this->getOne($pluralObjectName, $id);
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
        array $timeStampFields,
        $relatedPluralObjectName,
        $relatedPostData

    ){
        $pluralObjectName = $this->getPluralName();
        $initialState = $this->getOne($pluralObjectName, $id);
        sleep(1);
        $this->postOne($relatedPluralObjectName, $relatedPostData);
        $currentState = $this->getOne($pluralObjectName, $id);
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
        array $timeStampFields,
        $relatedPluralObjectName,
        $relatedId

    ){
        $pluralObjectName = $this->getPluralName();
        $initialState = $this->getOne($pluralObjectName, $id);
        sleep(1);
        $this->deleteOne($relatedPluralObjectName, $relatedId);
        $currentState = $this->getOne($pluralObjectName, $id);
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

    protected function getPluralName()
    {
        return $this->testName;
    }

    protected function getSingularName()
    {
        $pluralized = $this->getPluralName();
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
}
