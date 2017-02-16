<?php

namespace Tests\IliosApiBundle\Endpoints;

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
abstract class AbstractTest extends WebTestCase
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
     * @return array
     */
    public function filtersToTest()
    {
        return [];
    }

    /**
     * @return DataLoaderInterface
     */
    protected function getDataLoader()
    {
        $name = $this->getSingularName();
        $service = "ilioscore.dataloader.{$name}";

        return $this->container->get($service);
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
        $this->putTest($data, $postData);
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
    public function testFilters(array $dataKeys = [], array $filters = [])
    {
        if (empty($dataKeys) || empty($filters)) {
            $this->markTestSkipped('Missing filters tests for this endpoint');
            return;
        }
        $dataLoader = $this->getDataLoader();
        $all = $dataLoader->getAll();
        $expectedData = array_map(function($i) use ($all) {
            return $all[$i];
        }, $dataKeys);
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
        $this->assertEquals(
            $data,
            $returnedData
        );

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
            $this->assertEquals(
                $data[$i],
                $response
            );
        }

        return $responses;

    }

    protected function postTest($data, $postData)
    {
        $pluralObjectName = $this->getPluralName();
        $responseData = $this->postOne($pluralObjectName, $postData);

        $now = new DateTime();
        foreach ($this->getTimeStampFields() as $field) {
            $stamp = new DateTime($responseData[$field]);
            unset($responseData[$field]);
            $diff = $now->diff($stamp);
            $this->assertTrue($diff->y < 1, "The {$field} timestamp is within the last year");
        }

        $this->assertEquals(
            $data,
            $responseData
        );

        return $responseData;
    }

    protected function postOne($pluralObjectName, $postData)
    {
        $singularObjectName = Inflector::singularize($pluralObjectName);
        $this->createJsonRequest(
            'POST',
            $this->getUrl('ilios_api_post', ['version' => 'v1', 'object' => $pluralObjectName]),
            json_encode([$singularObjectName => $postData]),
            $this->getAuthenticatedUserToken()
        );
        $response = $this->client->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_CREATED);

        return json_decode($response->getContent(), true)[$pluralObjectName][0];
    }

    protected function badPostTest($data)
    {
        $pluralObjectName = $this->getPluralName();
        $singularObjectName = $this->getSingularName();
        $this->createJsonRequest(
            'POST',
            $this->getUrl('ilios_api_post', ['version' => 'v1', 'object' => $pluralObjectName]),
            json_encode([$singularObjectName => $data]),
            $this->getAuthenticatedUserToken()
        );

        $response = $this->client->getResponse();

        $this->assertEquals(
            Response::HTTP_BAD_REQUEST,
            $response->getStatusCode(),
            'Wrong Response Header.  Page Body: ' . substr($response->getContent(), 0, 200)
        );
    }

    public function relatedPostDataTest($data, $postData, $relationship, $related)
    {
        $responseData = $this->postTest($data, $postData);

        $newId = $responseData['id'];
        $this->assertTrue(array_key_exists($related, $postData), var_export($postData, true));
        foreach ($postData[$related] as $id) {
            $obj = $this->getOne($related, $id);
            $this->assertTrue(array_key_exists($relationship, $obj), var_export($obj, true));
            $this->assertTrue(in_array($newId, $obj[$relationship]));
        }
    }

    protected function putTest($data, $postData)
    {
        $pluralObjectName = $this->getPluralName();
        $responseData = $this->putOne($pluralObjectName, $data['id'], $postData);

        $now = new DateTime();
        foreach ($this->getTimeStampFields() as $field) {
            $stamp = new DateTime($responseData[$field]);
            unset($responseData[$field]);
            $diff = $now->diff($stamp);
            $this->assertTrue($diff->y < 1, "The {$field} timestamp is within the last year");
        }

        $this->assertEquals(
            $data,
            $responseData
        );

        return $responseData;
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

    protected function filterTest(array $filters, $expectedData)
    {
        $pluralObjectName = $this->getPluralName();
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
        $timeStampFields = $this->getTimeStampFields();
        $responseData = array_map(function ($arr) use ($timeStampFields) {
            foreach ($this->getTimeStampFields() as $field) {
                unset($arr[$field]);
            }
            return $arr;
        }, json_decode($response->getContent(), true)[$pluralObjectName]);

        $this->assertEquals(
            count($expectedData),
            count($responseData),
            'Wrong Number of responses returned from filter got: ' . var_export($responseData, true));
        foreach ($expectedData as $i => $data) {
            $this->assertEquals(
                $data,
                $responseData[$i]
            );
        }
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
}
