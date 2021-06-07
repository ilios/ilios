<?php

declare(strict_types=1);

namespace App\Tests;

use App\Service\InflectorFactory;
use App\Service\Timestamper;
use App\Tests\Fixture\LoadAuthenticationData;
use DateTime;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\Inflector\Inflector;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bridge\PhpUnit\ClockMock;
use App\Tests\DataLoader\DataLoaderInterface;
use App\Tests\Traits\JsonControllerTest;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;
use Faker\Factory as FakerFactory;
use Faker\Generator as FakerGenerator;

/**
 * Abstract Testing glue for endpoints
 */
abstract class AbstractEndpointTest extends WebTestCase
{
    use JsonControllerTest;
    use GetUrlTrait;

    protected string $apiVersion = 'v3';
    protected string $testName;
    protected KernelBrowser $kernelBrowser;
    protected AbstractDatabaseTool $databaseTool;
    private FakerGenerator $faker;
    private Inflector $inflector;
    protected ReferenceRepository $fixtures;

    public function setUp(): void
    {
        parent::setUp();
        $this->kernelBrowser = self::createClient();
        $this->kernelBrowser->followRedirects();

        $authFixtures = [
            LoadAuthenticationData::class,
        ];
        $testFixtures = $this->getFixtures();
        $fixtures = array_merge($authFixtures, $testFixtures);
        $this->databaseTool = $this->getContainer()->get(DatabaseToolCollection::class)->get();
        $executor = $this->databaseTool->loadFixtures($fixtures);
        $this->fixtures = $executor->getReferenceRepository();

        ClockMock::register(Timestamper::class);
        $this->inflector = InflectorFactory::create();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->kernelBrowser);
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

    protected function getContainer(): ContainerInterface
    {
        return $this->kernelBrowser->getContainer();
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
        return $this->inflector->singularize($pluralized);
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
        return $this->inflector->singularize($pluralized);
    }

    /**
     * @return FakerGenerator
     */
    protected function getFaker()
    {
        if (!isset($this->faker)) {
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
     * Transform JSON:API response into our normal data shape and compare it
     */
    protected function compareJsonApiData(array $expected, object $result)
    {
        $transformed = [
            'id' => (string) $result->id
        ];
        foreach ($result->attributes as $key => $value) {
            if (!is_null($value)) {
                $transformed[$key] = $value;
            }
        }
        foreach ($result->relationships as $key => $obj) {
            if (is_array($obj->data)) {
                $transformed[$key] = [];
                foreach ($obj->data as $item) {
                    $transformed[$key][] = $item->id;
                }
            } else {
                $transformed[$key] = $obj->data->id;
            }
        }

        // Remove empty relationships as they won't be present in JSON:API
        foreach ($expected as $key => $value) {
            if (is_array($value) && empty($value)) {
                unset($expected[$key]);
            }
        }

        $this->compareData($expected, $transformed);
    }

    /**
     * @return DataLoaderInterface
     */
    protected function getDataLoader()
    {
        $name = ucfirst($this->getCamelCasedSingularName());
        $service = "App\\Tests\\DataLoader\\{$name}Data";

        /** @var DataLoaderInterface $dataLoader */
        $dataLoader = $this->getContainer()->get($service);
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
        $this->makeJsonRequest($this->kernelBrowser, $method, $url, $content, $token, $files);
    }

    /**
     * Create a JSON:API request
     */
    protected function createJsonApiRequest(
        string $method,
        string $url,
        ?string $content,
        ?string $token,
        array $files = []
    ) {
        $this->makeJsonApiRequest($this->kernelBrowser, $method, $url, $content, $token, $files);
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
     * Test getting a single value from the JSON:API
     * @return mixed
     */
    protected function getOneJsonApiTest()
    {
        $endpoint = $this->getPluralName();
        $loader = $this->getDataLoader();
        $data = $loader->getOne();
        $returnedData = $this->getOneJsonApi($endpoint, (string) $data['id']);
        $this->assertSame($responseKey = $this->getCamelCasedPluralName(), $returnedData->type);

        foreach ($this->getTimeStampFields() as $field) {
            $stamp = new DateTime($returnedData->attributes->$field);
            unset($returnedData->attributes->$field);
            $now = new DateTime();
            $diff = $now->diff($stamp);
            $this->assertTrue($diff->y < 1, "The {$field} timestamp is within the last year");
        }
        $this->compareJsonApiData($data, $returnedData);

        return $returnedData;
    }

    /**
     * Get a single value from an API endpoint
     *
     * @param string $endpoint the name of the API endpoint
     * @param string $responseKey the key data is returned under
     * @param mixed $id the ID to fetch
     * @param string $version the version of the API endpoint
     *
     * @return mixed
     */
    protected function getOne($endpoint, $responseKey, $id, $version = null)
    {
        $version = $version ?: $this->apiVersion;
        $url = $this->getUrl(
            $this->kernelBrowser,
            "app_api_${endpoint}_getone",
            ['version' => $version, 'id' => $id]
        );
        $this->createJsonRequest(
            'GET',
            $url,
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );

        $response = $this->kernelBrowser->getResponse();

        if (Response::HTTP_NOT_FOUND === $response->getStatusCode()) {
            $this->fail("Unable to load url: {$url}");
        }

        $this->assertJsonResponse($response, Response::HTTP_OK);
        $content = json_decode($response->getContent(), true);
        $this->assertArrayHasKey($responseKey, $content);
        $this->assertCount(1, $content[$responseKey], var_export($content, true));
        return $content[$responseKey][0];
    }

    /**
     * Get a single value from a JSON:API endpoint
     */
    protected function getOneJsonApi(string $endpoint, string $id): object
    {
        $url = $this->getUrl(
            $this->kernelBrowser,
            "app_api_${endpoint}_getone",
            ['version' => $this->apiVersion, 'id' => $id]
        );
        $this->createJsonApiRequest(
            'GET',
            $url,
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );

        $response = $this->kernelBrowser->getResponse();

        if (Response::HTTP_NOT_FOUND === $response->getStatusCode()) {
            $this->fail("Unable to load url: {$url}");
        }

        $this->assertJsonApiResponse($response, Response::HTTP_OK);
        $content = json_decode($response->getContent());
        $this->assertCount(0, $content->included, var_export($content, true));
        $this->assertIsObject($content->data);
        $this->assertObjectHasAttribute('id', $content->data);
        $this->assertObjectHasAttribute('type', $content->data);
        $this->assertObjectHasAttribute('attributes', $content->data);
        $this->assertObjectHasAttribute('relationships', $content->data);

        return $content->data;
    }

    protected function getJsonApiIncludes(string $endpoint, string $id, string $include): array
    {
        $included = $this->getJsonApiIncludeContent($endpoint, $id, $include);
        return array_reduce($included, function (array $carry, object $obj) {
            if (!array_key_exists($obj->type, $carry)) {
                $carry[$obj->type] = [];
            }
            $carry[$obj->type][] = $obj->id;
            sort($carry[$obj->type]);

            return $carry;
        }, []);
    }

    protected function getJsonApiIncludeContent(string $endpoint, string $id, string $include): array
    {
        $url = $this->getUrl(
            $this->kernelBrowser,
            "app_api_${endpoint}_getone",
            ['version' => $this->apiVersion, 'id' => $id, 'include' => $include]
        );
        $this->createJsonApiRequest(
            'GET',
            $url,
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );

        $response = $this->kernelBrowser->getResponse();

        if (Response::HTTP_NOT_FOUND === $response->getStatusCode()) {
            $this->fail("Unable to load url: {$url}");
        }

        $this->assertJsonApiResponse($response, Response::HTTP_OK);
        $content = json_decode($response->getContent());
        $this->assertObjectHasAttribute('included', $content);

        return $content->included;
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
                $this->kernelBrowser,
                "app_api_${endpoint}_getall",
                ['version' => $this->apiVersion]
            ),
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );
        $response = $this->kernelBrowser->getResponse();

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
     * Get with limit and offset
     * @return mixed
     */
    protected function getAllWithLimitAndOffsetTest()
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $loader = $this->getDataLoader();
        $data = $loader->getAll();
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_${endpoint}_getall",
                ['version' => $this->apiVersion, 'limit' => 1, 'offset' => 0]
            ),
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );
        $response = $this->kernelBrowser->getResponse();

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
     * Get getting every piece of data in the test DB
     * @return mixed
     */
    protected function getAllJsonApiTest()
    {
        $endpoint = $this->getPluralName();
        $loader = $this->getDataLoader();
        $data = $loader->getAll();
        $this->createJsonApiRequest(
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_${endpoint}_getall",
                ['version' => $this->apiVersion]
            ),
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );
        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonApiResponse($response, Response::HTTP_OK);

        $content = json_decode($response->getContent());

        $this->assertCount(0, $content->included, var_export($content, true));
        $this->assertIsArray($content->data);

        $now = new DateTime();
        foreach ($content->data as $i => $item) {
            foreach ($this->getTimeStampFields() as $field) {
                $stamp = new DateTime($item->attributes->$field);
                unset($item->attributes->$field);
                $now = new DateTime();
                $diff = $now->diff($stamp);
                $this->assertTrue($diff->y < 1, "The {$field} timestamp is within the last year");
            }
            $this->assertObjectHasAttribute('id', $item);
            $this->assertObjectHasAttribute('type', $item);
            $this->assertObjectHasAttribute('attributes', $item);
            $this->assertObjectHasAttribute('relationships', $item);

            $this->compareJsonApiData($data[$i], $item);
        }

        return $content->data;
    }

    /**
     * Get getting every piece of data in the test DB
     * @return mixed
     */
    protected function getAllWithLimitAndOffsetJsonApiTest()
    {
        $endpoint = $this->getPluralName();
        $loader = $this->getDataLoader();
        $data = $loader->getAll();
        $this->createJsonApiRequest(
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_${endpoint}_getall",
                ['version' => $this->apiVersion, 'limit' => 1, 'offset' => 0]
            ),
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );
        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonApiResponse($response, Response::HTTP_OK);

        $content = json_decode($response->getContent());

        $this->assertCount(0, $content->included, var_export($content, true));
        $this->assertIsArray($content->data);

        $now = new DateTime();
        foreach ($content->data as $i => $item) {
            foreach ($this->getTimeStampFields() as $field) {
                $stamp = new DateTime($item->attributes->$field);
                unset($item->attributes->$field);
                $diff = $now->diff($stamp);
                $this->assertTrue($diff->y < 1, "The {$field} timestamp is within the last year");
            }
            $this->assertObjectHasAttribute('id', $item);
            $this->assertObjectHasAttribute('type', $item);
            $this->assertObjectHasAttribute('attributes', $item);
            $this->assertObjectHasAttribute('relationships', $item);

            $this->compareJsonApiData($data[$i], $item);
        }

        return $content->data;
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
     * Test saving new data to the JSON:API
     * @return mixed
     */
    protected function postJsonApiTest(object $postData, array $data)
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $responseData = $this->postOneJsonApi($postData);

        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getOne($endpoint, $responseKey, $responseData->id);

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
            if (is_string($a['id']) && is_string($b['id'])) {
                return strnatcasecmp($a['id'], $b['id']);
            }

            return $a['id'] <=> $b['id'];
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
     * Test saving new data to the JSON:API
     * @return mixed
     */
    protected function postManyJsonApiTest(object $postData, array $data)
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $responseData = $this->postManyJsonApi($postData);
        $ids = array_column($responseData, 'id');
        $filters = [
            'filters[id]' => $ids
        ];
        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getFiltered($endpoint, $responseKey, $filters);

        usort($fetchedResponseData, function ($a, $b) {
            if (is_string($a['id']) && is_string($b['id'])) {
                return strnatcasecmp($a['id'], $b['id']);
            }

            return $a['id'] <=> $b['id'];
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
     * @param string $version the version of the API endpoint
     *
     * @return mixed
     */
    protected function postOne($endpoint, $postKey, $responseKey, array $postData, $version = null)
    {
        $version = $version ?: $this->apiVersion;
        $this->createJsonRequest(
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_${endpoint}_post",
                ['version' => $version]
            ),
            json_encode([$postKey => $postData]),
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_CREATED);

        return json_decode($response->getContent(), true)[$responseKey][0];
    }

    /**
     * POST a single item to the JSON:API
     */
    protected function postOneJsonApi(object $postData): object
    {
        $endpoint = strtolower($postData->data->type);
        $this->createJsonApiRequest(
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_${endpoint}_post",
                ['version' => $this->apiVersion]
            ),
            json_encode($postData),
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonApiResponse($response, Response::HTTP_CREATED);
        $obj = json_decode($response->getContent());
        $this->assertIsObject($obj->data);
        $this->assertObjectHasAttribute('id', $obj->data);
        $this->assertObjectHasAttribute('type', $obj->data);
        $this->assertObjectHasAttribute('attributes', $obj->data);
        $this->assertObjectHasAttribute('relationships', $obj->data);

        return $obj->data;
    }

    /**
     * @param string $endpoint to send to
     * @param string $responseKey the data will be returned with
     * @param array $postData to send
     * @param string $version the version of the API endpoint
     *
     * @return mixed
     */
    protected function postMany($endpoint, $responseKey, array $postData, $version = null)
    {
        $version = $version ?: $this->apiVersion;
        $this->createJsonRequest(
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_${endpoint}_post",
                ['version' => $version]
            ),
            json_encode([$responseKey => $postData]),
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_CREATED);

        return json_decode($response->getContent(), true)[$responseKey];
    }

    /**
     * POST multiple items to the JSON:API
     */
    protected function postManyJsonApi(object $postData): array
    {
        $endpoint = strtolower($postData->data[0]->type);
        $this->createJsonApiRequest(
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_${endpoint}_post",
                ['version' => $this->apiVersion]
            ),
            json_encode($postData),
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonApiResponse($response, Response::HTTP_CREATED);
        $obj = json_decode($response->getContent());
        $this->assertIsArray($obj->data);
        foreach ($obj->data as $data) {
            $this->assertObjectHasAttribute('id', $data);
            $this->assertObjectHasAttribute('type', $data);
            $this->assertObjectHasAttribute('attributes', $data);
            $this->assertObjectHasAttribute('relationships', $data);
        }

        return $obj->data;
    }

    /**
     * Test POSTing bad data to the API
     * @param array $data
     * @param int $code
     */
    protected function badPostTest(array $data, $code = Response::HTTP_BAD_REQUEST)
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $this->createJsonRequest(
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_${endpoint}_post",
                ['version' => $this->apiVersion]
            ),
            json_encode([$responseKey => [$data]]),
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );

        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, $code);
    }

    /**
     * Test POSTing bad data to the API
     * @param array $data
     * @param int $code
     */
    protected function badPutTest(array $data, $id, $code = Response::HTTP_BAD_REQUEST)
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_${endpoint}_put",
                ['version' => $this->apiVersion, 'id' => $id]
            ),
            json_encode([$responseKey => [$data]]),
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );

        $response = $this->kernelBrowser->getResponse();

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
        $relatedName = null == $relatedName ? $related : $relatedName;
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
     * @param int $userId
     * @param string $version the version of the API endpoint
     *
     * @return mixed
     */
    protected function putOne($endpoint, $responseKey, $id, array $data, $new = false, $userId = 2, $version = null)
    {
        $version = $version ?: $this->apiVersion;
        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_${endpoint}_put",
                ['version' => $version, 'id' => $id]
            ),
            json_encode([$responseKey => $data]),
            $this->getTokenForUser($this->kernelBrowser, $userId)
        );
        $response = $this->kernelBrowser->getResponse();
        $expectedHeader = $new ? Response::HTTP_CREATED : Response::HTTP_OK;
        $this->assertJsonResponse($response, $expectedHeader);

        return json_decode($response->getContent(), true)[$responseKey];
    }

    /**
     * PUT a single item to the JSON:API
     */
    protected function patchOneJsonApi(object $data): object
    {
        $endpoint = strtolower($data->data->type);
        $this->createJsonApiRequest(
            'PATCH',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_${endpoint}_patch",
                ['version' => $this->apiVersion, 'id' => $data->data->id]
            ),
            json_encode($data),
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonApiResponse($response, Response::HTTP_OK);
        $obj = json_decode($response->getContent());
        $this->assertIsObject($obj->data);
        $this->assertObjectHasAttribute('id', $obj->data);
        $this->assertObjectHasAttribute('type', $obj->data);
        $this->assertObjectHasAttribute('attributes', $obj->data);
        $this->assertObjectHasAttribute('relationships', $obj->data);

        return $obj->data;
    }

    /**
     * Test putting a  single value to the API
     */
    protected function patchJsonApiTest(array $data, object $postData)
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $responseData = $this->patchOneJsonApi($postData);

        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getOne($endpoint, $responseKey, $responseData->id);

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
     * @param string $version the version of the API endpoint
     * @return null|Response
     */
    protected function deleteOne($endpoint, $id, $version = null)
    {
        $version = $version ?: $this->apiVersion;
        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_${endpoint}_delete",
                ['version' => $version, 'id' => $id]
            ),
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );
        $response = $this->kernelBrowser->getResponse();


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
                $this->kernelBrowser,
                "app_api_${endpoint}_getone",
                ['version' => $this->apiVersion, 'id' => $badId]
            ),
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );

        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_NOT_FOUND);
    }

    /**
     * Test that a filter returns the expected data
     * @param array $filters we are using
     * @param array $expectedData we hope to see
     * @param int $userId
     */
    protected function filterTest(array $filters, array $expectedData, int $userId = 2)
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $filteredData = $this->getFiltered($endpoint, $responseKey, $filters, $userId);

        $timeStampFields = $this->getTimeStampFields();
        $responseData = array_map(function ($arr) use ($timeStampFields) {
            foreach ($timeStampFields as $field) {
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
     * Test that a filter returns the expected data
     */
    protected function jsonApiFilterTest(array $filters, array $expectedData)
    {
        $endpoint = $this->getPluralName();
        $parameters = array_merge([
            'version' => $this->apiVersion,
        ], $filters);
        $this->createJsonApiRequest(
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_${endpoint}_getall",
                $parameters
            ),
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );
        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonApiResponse($response, Response::HTTP_OK);

        $content = json_decode($response->getContent());

        $this->assertCount(0, $content->included, var_export($content, true));
        $this->assertIsArray($content->data);

        $this->assertEquals(
            count($expectedData),
            count($content->data),
            'Wrong Number of responses returned from filter got: ' . var_export($content->data, true)
        );
        foreach ($expectedData as $i => $data) {
            $responseData = $content->data[$i];
            foreach ($this->getTimeStampFields() as $field) {
                unset($data[$field]);
                unset($responseData->attributes->$field);
            }
            $this->compareJsonApiData($data, $responseData);
        }
    }

    /**
     * Get data from the API using filter parameters
     * @param $endpoint
     * @param $responseKey
     * @param array $filters
     * @param int $userId
     * @param string $version
     * @return mixed
     */
    protected function getFiltered($endpoint, $responseKey, array $filters, int $userId = 2, $version = null)
    {
        $version = $version ?: $this->apiVersion;
        $parameters = array_merge([
            'version' => $version,
        ], $filters);
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_${endpoint}_getall",
                $parameters
            ),
            null,
            $this->getTokenForUser($this->kernelBrowser, $userId)
        );

        $response = $this->kernelBrowser->getResponse();

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
            'version' => $this->apiVersion,
        ], $badFilters);
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_${endpoint}_getall",
                $parameters
            ),
            null,
            $this->getAuthenticatedUserToken($this->kernelBrowser)
        );

        $response = $this->kernelBrowser->getResponse();

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
        ClockMock::withClockMock(true);
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $initialState = $this->getOne($endpoint, $responseKey, $id);
        sleep(10);
        $this->putOne($relatedEndpoint, $relatedResponseKey, $relatedData['id'], $relatedData);
        $currentState = $this->getOne($endpoint, $responseKey, $id);
        foreach ($this->getTimeStampFields() as $field) {
            $initialStamp = new DateTime($initialState[$field]);
            $currentStamp = new DateTime($currentState[$field]);

            $diff = $currentStamp->getTimestamp() - $initialStamp->getTimestamp();
            $this->assertTrue(
                $diff > 0,
                'The timestamp has increased.  Original: ' . $initialStamp->format('c') .
                ' Now: ' . $currentStamp->format('c')
            );
        }
        ClockMock::withClockMock(false);
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
        ClockMock::withClockMock(true);
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $initialState = $this->getOne($endpoint, $responseKey, $id);
        sleep(10);
        $this->postMany($relatedPluralObjectName, $relatedResponseKey, [$relatedPostData]);
        $currentState = $this->getOne($endpoint, $responseKey, $id);
        foreach ($this->getTimeStampFields() as $field) {
            $initialStamp = new DateTime($initialState[$field]);
            $currentStamp = new DateTime($currentState[$field]);

            $diff = $currentStamp->getTimestamp() - $initialStamp->getTimestamp();
            $this->assertTrue(
                $diff > 0,
                'The timestamp has increased.  Original: ' . $initialStamp->format('c') .
                ' Now: ' . $currentStamp->format('c')
            );
        }
        ClockMock::withClockMock(false);
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
        ClockMock::withClockMock(true);
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $initialState = $this->getOne($endpoint, $responseKey, $id);
        sleep(10);
        $this->deleteOne($relatedPluralObjectName, $relatedId);
        $currentState = $this->getOne($endpoint, $responseKey, $id);
        foreach ($this->getTimeStampFields() as $field) {
            $initialStamp = new DateTime($initialState[$field]);
            $currentStamp = new DateTime($currentState[$field]);

            $diff = $currentStamp->getTimestamp() - $initialStamp->getTimestamp();
            $this->assertTrue(
                $diff > 0,
                'The timestamp has increased.  Original: ' . $initialStamp->format('c') .
                ' Now: ' . $currentStamp->format('c')
            );
        }
        ClockMock::withClockMock(false);
    }
}
