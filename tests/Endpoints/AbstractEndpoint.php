<?php

declare(strict_types=1);

namespace App\Tests\Endpoints;

use App\Service\InflectorFactory;
use App\Tests\DataLoader\DataLoaderInterface;
use App\Tests\Fixture\LoadAuthenticationData;
use App\Tests\Fixture\LoadSchoolData;
use App\Tests\Fixture\LoadServiceTokenData;
use App\Tests\GetUrlTrait;
use App\Tests\Traits\TestableJsonController;
use DateTime;
use Doctrine\Common\DataFixtures\ReferenceRepository;
use Doctrine\Inflector\Inflector;
use Exception;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

use function array_key_exists;
use function get_object_vars;
use function implode;
use function in_array;
use function is_null;
use function json_decode;
use function json_encode;
use function var_export;

/**
 * Abstract Testing glue for endpoints
 */
abstract class AbstractEndpoint extends WebTestCase
{
    use TestableJsonController;
    use GetUrlTrait;

    protected string $apiVersion = 'v3';
    protected string $testName;
    final protected KernelBrowser $kernelBrowser;
    final protected AbstractDatabaseTool $databaseTool;
    private Inflector $inflector;
    final protected ReferenceRepository $fixtures;

    public function setUp(): void
    {
        parent::setUp();
        $this->kernelBrowser = self::createClient();
        $this->kernelBrowser->followRedirects();

        $authFixtures = [
            LoadAuthenticationData::class,
            LoadSchoolData::class,
            LoadServiceTokenData::class,
        ];
        $testFixtures = $this->getFixtures();
        $fixtures = array_merge($authFixtures, $testFixtures);
        $databaseToolCollection = self::getContainer()->get(DatabaseToolCollection::class);
        $this->databaseTool = $databaseToolCollection->get();
        $executor = $this->databaseTool->loadFixtures($fixtures);
        $this->fixtures = $executor->getReferenceRepository();

        $this->inflector = InflectorFactory::create();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->kernelBrowser);
        unset($this->fixtures);
        unset($this->databaseTool);
        unset($this->inflector);
    }


    protected function getFixtures(): array
    {
        return [];
    }


    protected function getPluralName(): string
    {
        return strtolower($this->testName);
    }


    protected function getSingularName(): string
    {
        $pluralized = $this->getPluralName();
        return $this->inflector->singularize($pluralized);
    }


    protected function getCamelCasedPluralName(): ?string
    {
        return $this->testName;
    }


    protected function getCamelCasedSingularName(): string
    {
        $pluralized = $this->getCamelCasedPluralName();
        return $this->inflector->singularize($pluralized);
    }

    /**
     * An overridable way to do the field comparison
     * So those endpoints which don't return all data
     * like Users::alerts[] will be able to do their comparison
     *
     */
    protected function compareData(array $expected, array $result): void
    {

        $this->assertEquals(
            $expected,
            $result
        );
    }

    /**
     * Transform JSON:API response into our normal data shape and compare it
     */
    protected function compareJsonApiData(array $expected, object $result): void
    {
        $transformed = [
            'id' => (string) $result->id,
        ];
        foreach ($result->attributes as $key => $value) {
            if (!is_null($value)) {
                $transformed[$key] = $value;
            }
        }
        foreach ($result->relationships as $key => $obj) {
            if (is_null($obj->data)) {
                $transformed[$key] = null;
            } elseif (is_array($obj->data)) {
                $transformed[$key] = [];
                foreach ($obj->data as $item) {
                    $transformed[$key][] = $item->id;
                }
            } else {
                $transformed[$key] = $obj->data->id;
            }
        }
        $this->compareData($expected, $transformed);
    }

    /**
     * Overridable data comparison for GraphQL API
     * Because GraphQL returns null values we have to do some special work to compare it to our expected
     * data. Our DataProviders sometimes have null values set, but often just omit these values.
     */
    protected function compareGraphQLData(array $expected, object $result): void
    {
        foreach (get_object_vars($result) as $key => $value) {
            if (is_null($value)) {
                if (array_key_exists($key, $expected)) {
                    $this->assertNull($expected[$key]);
                }
            } else {
                $this->assertArrayHasKey($key, $expected);
                $this->assertEquals($expected[$key], $value, "'$key' doesn't match");
            }
        }
    }

    protected function getDataLoader(): DataLoaderInterface
    {
        $name = ucfirst($this->getCamelCasedSingularName());
        $service = "App\\Tests\\DataLoader\\{$name}Data";

        /** @var DataLoaderInterface $dataLoader */
        $dataLoader = self::getContainer()->get($service);
        return $dataLoader;
    }


    protected function getTimeStampFields(): array
    {
        return [];
    }

    /**
     * Create a JSON request
     *
     */
    protected function createJsonRequest(
        string $method,
        string $url,
        ?string $content = null,
        ?string $jwt = null,
        array $files = []
    ): void {
        $this->makeJsonRequest($this->kernelBrowser, $method, $url, $content, $jwt, $files);
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
    ): void {
        $this->makeJsonApiRequest($this->kernelBrowser, $method, $url, $content, $token, $files);
    }

    /**
     * Create a GraphQL request
     */
    protected function createGraphQLRequest(
        ?string $content,
        ?string $jwt
    ): void {
        $headers = [];

        if (! empty($jwt)) {
            $headers['HTTP_X-JWT-Authorization'] = 'Token ' . $jwt;
        }

        $this->kernelBrowser->request(
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_graphql_index"
            ),
            [],
            [],
            $headers,
            $content
        );
    }

    /**
     * Test getting a single value from the API
     */
    protected function getOneTest(string $jwt): array
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $loader = $this->getDataLoader();
        $data = $loader->getOne();
        $returnedData = $this->getOne($endpoint, $responseKey, $data['id'], $jwt);

        foreach ($this->getTimeStampFields() as $field) {
            $stamp = new DateTime($returnedData[$field]);
            unset($returnedData[$field]);
            $now = new DateTime();
            $diff = $now->diff($stamp);
            $this->assertTrue($diff->y < 1, "The $field timestamp is within the last year");
        }
        $prunedData = $this->pruneData($data);
        $this->compareData($prunedData, $returnedData);

        return $returnedData;
    }

    /**
     * Test getting a single value from the JSON:API
     */
    protected function getOneJsonApiTest(string $jwt): object
    {
        $endpoint = $this->getPluralName();
        $loader = $this->getDataLoader();
        $data = $loader->getOne();
        $returnedData = $this->getOneJsonApi($endpoint, (string) $data['id'], $jwt);
        $this->assertSame($this->getCamelCasedPluralName(), $returnedData->type);

        foreach ($this->getTimeStampFields() as $field) {
            $stamp = new DateTime($returnedData->attributes->$field);
            unset($returnedData->attributes->$field);
            $now = new DateTime();
            $diff = $now->diff($stamp);
            $this->assertTrue($diff->y < 1, "The $field timestamp is within the last year");
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
     * @param string $jwt an API access token
     * @param ?string $version the version of the API endpoint
     * @return array The deserialized response data
     */
    protected function getOne(
        string $endpoint,
        string $responseKey,
        mixed $id,
        string $jwt,
        ?string $version = null
    ): array {
        $version = $version ?: $this->apiVersion;
        $url = $this->getUrl(
            $this->kernelBrowser,
            "app_api_{$endpoint}_getone",
            ['version' => $version, 'id' => $id]
        );
        $this->createJsonRequest(
            'GET',
            $url,
            null,
            $jwt,
        );

        $response = $this->kernelBrowser->getResponse();

        if (Response::HTTP_NOT_FOUND === $response->getStatusCode()) {
            $this->fail("Unable to load url: $url");
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
    protected function getOneJsonApi(string $endpoint, string $id, string $jwt): object
    {
        $url = $this->getUrl(
            $this->kernelBrowser,
            "app_api_{$endpoint}_getone",
            ['version' => $this->apiVersion, 'id' => $id]
        );
        $this->createJsonApiRequest(
            'GET',
            $url,
            null,
            $jwt
        );

        $response = $this->kernelBrowser->getResponse();

        if (Response::HTTP_NOT_FOUND === $response->getStatusCode()) {
            $this->fail("Unable to load url: $url");
        }

        $this->assertJsonApiResponse($response, Response::HTTP_OK);
        $content = json_decode($response->getContent());
        $this->assertCount(0, $content->included, var_export($content, true));
        $this->assertIsObject($content->data);
        $this->assertTrue(property_exists($content->data, 'id'));
        $this->assertTrue(property_exists($content->data, 'type'));
        $this->assertTrue(property_exists($content->data, 'attributes'));
        $this->assertTrue(property_exists($content->data, 'relationships'));

        return $content->data;
    }

    protected function getJsonApiIncludes(string $endpoint, string $id, string $include, string $jwt): array
    {
        $included = $this->getJsonApiIncludeContent($endpoint, $id, $include, $jwt);
        return array_reduce($included, function (array $carry, object $obj) {
            if (!array_key_exists($obj->type, $carry)) {
                $carry[$obj->type] = [];
            }
            $carry[$obj->type][] = $obj->id;
            sort($carry[$obj->type]);

            return $carry;
        }, []);
    }

    protected function getJsonApiIncludeContent(string $endpoint, string $id, string $include, string $jwt): array
    {
        $url = $this->getUrl(
            $this->kernelBrowser,
            "app_api_{$endpoint}_getone",
            ['version' => $this->apiVersion, 'id' => $id, 'include' => $include]
        );
        $this->createJsonApiRequest(
            'GET',
            $url,
            null,
            $jwt
        );

        $response = $this->kernelBrowser->getResponse();

        if (Response::HTTP_NOT_FOUND === $response->getStatusCode()) {
            $this->fail("Unable to load url: $url");
        }

        $this->assertJsonApiResponse($response, Response::HTTP_OK);
        $content = json_decode($response->getContent());
        $this->assertTrue(property_exists($content, 'included'));

        return $content->included;
    }

    /**
     * Get getting every piece of data in the test DB
     */
    protected function getAllTest(string $jwt): array
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $loader = $this->getDataLoader();
        $data = $loader->getAll();
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_{$endpoint}_getall",
                ['version' => $this->apiVersion]
            ),
            null,
            $jwt
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
                $this->assertTrue($diff->y < 1, "The $field timestamp is within the last year");
            }
            $prunedData = $this->pruneData($data[$i]);
            $this->compareData($prunedData, $response);
        }

        return $responses;
    }

    /**
     * Get with limit and offset
     */
    protected function getAllWithLimitAndOffsetTest(string $jwt): array
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $loader = $this->getDataLoader();
        $data = $loader->getAll();
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_{$endpoint}_getall",
                ['version' => $this->apiVersion, 'limit' => 1, 'offset' => 0]
            ),
            null,
            $jwt
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
                $this->assertTrue($diff->y < 1, "The $field timestamp is within the last year");
            }
            $prunedData = $this->pruneData($data[$i]);
            $this->compareData($prunedData, $response);
        }

        return $responses;
    }

    /**
     * Get getting every piece of data in the test DB
     */
    protected function getAllJsonApiTest(string $jwt): array
    {
        $endpoint = $this->getPluralName();
        $loader = $this->getDataLoader();
        $data = $loader->getAll();
        $this->createJsonApiRequest(
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_{$endpoint}_getall",
                ['version' => $this->apiVersion]
            ),
            null,
            $jwt
        );
        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonApiResponse($response, Response::HTTP_OK);

        $content = json_decode($response->getContent());

        $this->assertCount(0, $content->included, var_export($content, true));
        $this->assertIsArray($content->data);

        foreach ($content->data as $i => $item) {
            foreach ($this->getTimeStampFields() as $field) {
                $stamp = new DateTime($item->attributes->$field);
                unset($item->attributes->$field);
                $now = new DateTime();
                $diff = $now->diff($stamp);
                $this->assertTrue($diff->y < 1, "The $field timestamp is within the last year");
            }
            $this->assertTrue(property_exists($item, 'id'));
            $this->assertTrue(property_exists($item, 'type'));
            $this->assertTrue(property_exists($item, 'attributes'));
            $this->assertTrue(property_exists($item, 'relationships'));

            $this->compareJsonApiData($data[$i], $item);
        }

        return $content->data;
    }

    /**
     * Get getting every piece of data in the test DB
     */
    protected function getAllWithLimitAndOffsetJsonApiTest(string $jwt): array
    {
        $endpoint = $this->getPluralName();
        $loader = $this->getDataLoader();
        $data = $loader->getAll();
        $this->createJsonApiRequest(
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_{$endpoint}_getall",
                ['version' => $this->apiVersion, 'limit' => 1, 'offset' => 0]
            ),
            null,
            $jwt
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
                $this->assertTrue($diff->y < 1, "The $field timestamp is within the last year");
            }
            $this->assertTrue(property_exists($item, 'id'));
            $this->assertTrue(property_exists($item, 'type'));
            $this->assertTrue(property_exists($item, 'attributes'));
            $this->assertTrue(property_exists($item, 'relationships'));

            $this->compareJsonApiData($data[$i], $item);
        }

        return $content->data;
    }

    protected function getAllGraphQLTest(string $jwt): array
    {
        $name = $this->getCamelCasedPluralName();
        $loader = $this->getDataLoader();
        $scalarFields = $loader->getScalarFields();
        $fields = implode(', ', $scalarFields);
        $data = $loader->getAll();
        $this->createGraphQLRequest(
            json_encode([
                'query' => "query { $name { $fields }}",
            ]),
            $jwt
        );
        $response = $this->kernelBrowser->getResponse();

        $this->assertGraphQLResponse($response);

        $content = json_decode($response->getContent());

        $this->assertObjectHasProperty('data', $content);
        $this->assertIsObject($content->data);
        $this->assertObjectNotHasProperty('errors', $content);
        $this->assertIsArray($content->data->{$name});

        $now = new DateTime();
        $timeStampFields = $this->getTimeStampFields();
        foreach ($content->data->{$name} as $i => $item) {
            foreach ($scalarFields as $f) {
                if (in_array($f, $timeStampFields)) {
                    $stamp = new DateTime($item->{$f});
                    $diff = $now->diff($stamp);
                    $this->assertTrue($diff->y < 1, "The $f timestamp is within the last year");
                    unset($item->{$f});
                } else {
                    $this->assertTrue(property_exists($item, $f));
                }
            }
            $this->compareGraphQLData($data[$i], $item);
        }

        return $content->data->{$name};
    }

    protected function getSomeGraphQLTest(string $jwt): array
    {
        $loader = $this->getDataLoader();
        $idField = $loader->getIdField();
        $data = $loader->getOne();
        $result = $this->getGraphQLFiltered($idField, [$idField => $data[$idField]], $jwt);
        $this->assertCount(1, $result);
        $this->assertTrue(property_exists($result[0], $idField));
        $this->assertEquals($data[$idField], $result[0]->{$idField});

        return $result;
    }

    /**
     * Test that a filter returns the expected data for a graphql query
     */
    protected function graphQLFilterTest(array $filters, array $expectedIds, string $jwt): void
    {
        $idField = $this->getDataLoader()->getIdField();
        $result = $this->getGraphQLFiltered($idField, $filters, $jwt);

        $this->assertCount(count($expectedIds), $result);
        $this->assertCount(
            count($expectedIds),
            $result,
            'Wrong Number of responses returned from filter got: ' . var_export($result, true)
        );

        $ids = array_column($result, $idField);
        foreach ($expectedIds as $id) {
            $this->assertContains($id, $ids, "ID ($id) Missing from response:" . var_export($ids, true));
        }
    }

    /**
     * Test saving new data to the API
     *
     */
    protected function postTest(array $data, array $postData, string $jwt): array
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $postKey = $this->getCamelCasedSingularName();
        $responseData = $this->postOne($endpoint, $postKey, $responseKey, $postData, $jwt);
        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getOne($endpoint, $responseKey, $responseData['id'], $jwt);

        $now = new DateTime();
        foreach ($this->getTimeStampFields() as $field) {
            $stamp = new DateTime($fetchedResponseData[$field]);
            unset($fetchedResponseData[$field]);
            $diff = $now->diff($stamp);
            $this->assertTrue($diff->y < 1, "The $field timestamp is within the last year");
        }
        $prunedData = $this->pruneData($data);
        $this->compareData($prunedData, $fetchedResponseData);

        return $fetchedResponseData;
    }

    /**
     * Test saving new data to the JSON:API
     */
    protected function postJsonApiTest(object $postData, array $data, string $jwt): array
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $responseData = $this->postOneJsonApi($postData, $jwt);

        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getOne($endpoint, $responseKey, $responseData->id, $jwt);

        $now = new DateTime();
        foreach ($this->getTimeStampFields() as $field) {
            $stamp = new DateTime($fetchedResponseData[$field]);
            unset($fetchedResponseData[$field]);
            $diff = $now->diff($stamp);
            $this->assertTrue($diff->y < 1, "The $field timestamp is within the last year");
        }
        $prunedData = $this->pruneData($data);
        $this->compareData($prunedData, $fetchedResponseData);

        return $fetchedResponseData;
    }

    /**
     * Test POSTing an array of similar items to the API
     */
    protected function postManyTest(array $data, string $jwt): array
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $responseData = $this->postMany($endpoint, $responseKey, $data, $jwt);
        $ids = array_map(fn(array $arr) => $arr['id'], $responseData);
        $filters = [
            'filters[id]' => $ids,
            'limit' => count($ids),
        ];
        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getFiltered($endpoint, $responseKey, $filters, $jwt);

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
                $this->assertTrue($diff->y < 1, "The $field timestamp is within the last year");
            }
            $prunedData = $this->pruneData($datum);
            $this->compareData($prunedData, $response);
        }

        return $fetchedResponseData;
    }

    /**
     * Test saving new data to the JSON:API
     */
    protected function postManyJsonApiTest(object $postData, array $data, string $jwt): array
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $responseData = $this->postManyJsonApi($postData, $jwt);
        $ids = array_column($responseData, 'id');
        $filters = [
            'filters[id]' => $ids,
        ];
        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getFiltered($endpoint, $responseKey, $filters, $jwt);

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
                $this->assertTrue($diff->y < 1, "The $field timestamp is within the last year");
            }
            $prunedData = $this->pruneData($datum);
            $this->compareData($prunedData, $response);
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
     * @param string $jwt an API access token
     * @param ?string $version the version of the API endpoint
     * @return array The deserialized response data
     */
    protected function postOne(
        string $endpoint,
        string $postKey,
        string $responseKey,
        array $postData,
        string $jwt,
        ?string $version = null,
    ): array {
        $version = $version ?: $this->apiVersion;
        $this->createJsonRequest(
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_{$endpoint}_post",
                ['version' => $version]
            ),
            json_encode([$postKey => $postData]),
            $jwt
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_CREATED);

        return json_decode($response->getContent(), true)[$responseKey][0];
    }

    /**
     * POST a single item to the JSON:API
     */
    protected function postOneJsonApi(object $postData, string $jwt): object
    {
        $endpoint = strtolower($postData->data->type);
        $this->createJsonApiRequest(
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_{$endpoint}_post",
                ['version' => $this->apiVersion]
            ),
            json_encode($postData),
            $jwt
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonApiResponse($response, Response::HTTP_CREATED);
        $obj = json_decode($response->getContent());
        $this->assertIsObject($obj->data);
        $this->assertTrue(property_exists($obj->data, 'id'));
        $this->assertTrue(property_exists($obj->data, 'type'));
        $this->assertTrue(property_exists($obj->data, 'attributes'));
        $this->assertTrue(property_exists($obj->data, 'relationships'));

        return $obj->data;
    }

    /**
     * @param string $endpoint to send to
     * @param string $responseKey the data will be returned with
     * @param array $postData to send
     * @param string $jwt an API access token
     * @param ?string $version the version of the API endpoint
     */
    protected function postMany(
        string $endpoint,
        string $responseKey,
        array $postData,
        string $jwt,
        ?string $version = null
    ): array {
        $version = $version ?: $this->apiVersion;
        $this->createJsonRequest(
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_{$endpoint}_post",
                ['version' => $version]
            ),
            json_encode([$responseKey => $postData]),
            $jwt
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_CREATED);

        return json_decode($response->getContent(), true)[$responseKey];
    }

    /**
     * POST multiple items to the JSON:API
     */
    protected function postManyJsonApi(object $postData, string $jwt): array
    {
        $endpoint = strtolower($postData->data[0]->type);
        $this->createJsonApiRequest(
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_{$endpoint}_post",
                ['version' => $this->apiVersion]
            ),
            json_encode($postData),
            $jwt
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonApiResponse($response, Response::HTTP_CREATED);
        $obj = json_decode($response->getContent());
        $this->assertIsArray($obj->data);
        foreach ($obj->data as $data) {
            $this->assertTrue(property_exists($data, 'id'));
            $this->assertTrue(property_exists($data, 'type'));
            $this->assertTrue(property_exists($data, 'attributes'));
            $this->assertTrue(property_exists($data, 'relationships'));
        }

        return $obj->data;
    }

    /**
     * Test POSTing bad data to the API
     */
    protected function badPostTest(array $data, string $jwt, int $code = Response::HTTP_BAD_REQUEST): void
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $this->createJsonRequest(
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_{$endpoint}_post",
                ['version' => $this->apiVersion]
            ),
            json_encode([$responseKey => [$data]]),
            $jwt
        );

        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, $code);
    }

    /**
     * Test POSTing without authentication to the API
     */
    protected function anonymousDeniedPostTest(array $data): void
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $this->createJsonRequest(
            'POST',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_{$endpoint}_post",
                ['version' => $this->apiVersion]
            ),
            json_encode([$responseKey => [$data]]),
        );

        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test POSTing bad data to the API
     */
    protected function badPutTest(array $data, mixed $id, string $jwt, int $code = Response::HTTP_BAD_REQUEST): void
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_{$endpoint}_put",
                ['version' => $this->apiVersion, 'id' => $id]
            ),
            json_encode([$responseKey => [$data]]),
            $jwt
        );

        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, $code);
    }

    /**
     * Test PUTing as anonymous to the API
     */
    protected function anonymousDeniedPutTest(array $data): void
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_{$endpoint}_put",
                ['version' => $this->apiVersion, 'id' => $data['id']]
            ),
            json_encode([$responseKey => [$data]]),
        );

        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * When relational data is sent to the API ensure it
     * is recorded on the non-owning side of the relationship
     *
     * @param array $data to match with
     * @param array $postData to send
     * @param string $jwt an API access token
     * @param string $relationship the test target has to the subject
     * @param string $related the name of the related data
     */
    public function relatedPostDataTest(
        array $data,
        array $postData,
        string $jwt,
        string $relationship,
        string $related,
        ?string $relatedName = null
    ): void {
        $responseData = $this->postTest($data, $postData, $jwt);
        $newId = $responseData['id'];
        $relatedName = null == $relatedName ? $related : $relatedName;
        $this->assertArrayHasKey($relatedName, $postData, 'Missing related key: ' . var_export($postData, true));
        foreach ($postData[$relatedName] as $id) {
            $obj = $this->getOne(strtolower($related), $related, $id, $jwt);
            $this->assertArrayHasKey($relationship, $obj, var_export($obj, true));
            $this->assertTrue(in_array($newId, $obj[$relationship]));
        }
    }

    /**
     * Test putting a  single value to the API
     * @param bool $new if we are expecting this data to create a new item
     */
    protected function putTest(array $data, array $postData, mixed $id, string $jwt, bool $new = false): array
    {
        $endpoint = $this->getPluralName();
        $putResponseKey = $this->getCamelCasedSingularName();
        $getResponseKey = $this->getCamelCasedPluralName();
        $responseData = $this->putOne($endpoint, $putResponseKey, $id, $postData, $jwt, $new);
        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getOne($endpoint, $getResponseKey, $responseData['id'], $jwt);

        $now = new DateTime();
        foreach ($this->getTimeStampFields() as $field) {
            $stamp = new DateTime($fetchedResponseData[$field]);
            unset($fetchedResponseData[$field]);
            $diff = $now->diff($stamp);
            $this->assertTrue($diff->y < 1, "The $field timestamp is within the last year");
        }

        $prunedData = $this->pruneData($data);
        $this->compareData($prunedData, $fetchedResponseData);

        return $fetchedResponseData;
    }

    /**
     * Put a single item into the API
     *
     * @param string $endpoint we are testing
     * @param string $responseKey we expect to be returned
     * @param mixed $id of the data
     * @param array $data we are changing
     * @param string $jwt an API access token
     * @param bool $new if this is expected to generate new data instead of updating existing data
     * @param ?string $version the version of the API endpoint
     * @return array The deserialized response data
     */
    protected function putOne(
        string $endpoint,
        string $responseKey,
        mixed $id,
        array $data,
        string $jwt,
        bool $new = false,
        ?string $version = null,
    ): array {
        $version = $version ?: $this->apiVersion;
        $this->createJsonRequest(
            'PUT',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_{$endpoint}_put",
                ['version' => $version, 'id' => $id]
            ),
            json_encode([$responseKey => $data]),
            $jwt
        );
        $response = $this->kernelBrowser->getResponse();
        $expectedHeader = $new ? Response::HTTP_CREATED : Response::HTTP_OK;
        $this->assertJsonResponse($response, $expectedHeader);

        return json_decode($response->getContent(), true)[$responseKey];
    }

    /**
     * PUT a single item to the JSON:API
     *
     */
    protected function patchOneJsonApi(object $data, string $jwt): object
    {
        $endpoint = strtolower($data->data->type);
        $this->createJsonApiRequest(
            'PATCH',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_{$endpoint}_patch",
                ['version' => $this->apiVersion, 'id' => $data->data->id]
            ),
            json_encode($data),
            $jwt
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonApiResponse($response, Response::HTTP_OK);
        $obj = json_decode($response->getContent());
        $this->assertIsObject($obj->data);
        $this->assertTrue(property_exists($obj->data, 'id'));
        $this->assertTrue(property_exists($obj->data, 'type'));
        $this->assertTrue(property_exists($obj->data, 'attributes'));
        $this->assertTrue(property_exists($obj->data, 'relationships'));

        return $obj->data;
    }

    /**
     * Test putting a  single value to the API
     */
    protected function patchJsonApiTest(array $data, object $postData, string $jwt): array
    {

        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $responseData = $this->patchOneJsonApi($postData, $jwt);

        //re-fetch the data to test persistence
        $fetchedResponseData = $this->getOne($endpoint, $responseKey, $responseData->id, $jwt);

        $now = new DateTime();
        foreach ($this->getTimeStampFields() as $field) {
            $stamp = new DateTime($fetchedResponseData[$field]);
            unset($fetchedResponseData[$field]);
            $diff = $now->diff($stamp);
            $this->assertTrue($diff->y < 1, "The $field timestamp is within the last year");
        }

        $prunedData = $this->pruneData($data);
        $this->compareData($prunedData, $fetchedResponseData);

        return $fetchedResponseData;
    }

    /**
     * Test PATCHing as anonymous to the API
     */
    protected function anonymousDeniedPatchTest(array $data): void
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $this->createJsonRequest(
            'PATCH',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_{$endpoint}_patch",
                ['version' => $this->apiVersion, 'id' => $data['id']]
            ),
            json_encode([$responseKey => [$data]]),
        );

        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test deleting an object from the API
     *
     */
    protected function deleteTest(mixed $id, string $jwt): void
    {
        $endpoint = $this->getPluralName();
        $this->deleteOne($endpoint, $id, $jwt);
        $this->notFoundTest($id, $jwt);
    }

    /**
     * Delete an object from the API
     * @param string $endpoint we are testing
     * @param mixed $id we want to delete
     * @param string $jwt an API access token
     * @param ?string $version the version of the API endpoint
     */
    protected function deleteOne(string $endpoint, mixed $id, string $jwt, ?string $version = null): Response
    {
        $version = $version ?: $this->apiVersion;
        $this->createJsonRequest(
            'DELETE',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_{$endpoint}_delete",
                ['version' => $version, 'id' => $id]
            ),
            null,
            $jwt
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertJsonResponse($response, Response::HTTP_NO_CONTENT, false);
        return $response;
    }

    /**
     * Ensure that a bad ID returns a 404
     *
     */
    protected function notFoundTest(mixed $badId, string $jwt): void
    {
        $endpoint = $this->getPluralName();
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_{$endpoint}_getone",
                ['version' => $this->apiVersion, 'id' => $badId]
            ),
            null,
            $jwt
        );

        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_NOT_FOUND);
    }

    /**
     * Ensure that anonymous users cannot access the resource
     */
    protected function anonymousAccessDeniedOneTest(): void
    {
        $endpoint = $this->getPluralName();
        $loader = $this->getDataLoader();
        $data = $loader->getOne();
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_{$endpoint}_getone",
                ['version' => $this->apiVersion, 'id' => $data['id']]
            ),
        );

        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Ensure that anonymous users cannot access the resource
     */
    protected function anonymousAccessDeniedAllTest(): void
    {
        $endpoint = $this->getPluralName();
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_{$endpoint}_getall",
                ['version' => $this->apiVersion]
            ),
        );

        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_UNAUTHORIZED);
    }

    /**
     * Test that a filter returns the expected data
     * @param array $filters we are using
     * @param array $expectedData we hope to see
     */
    protected function filterTest(array $filters, array $expectedData, string $jwt): void
    {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $filteredData = $this->getFiltered($endpoint, $responseKey, $filters, $jwt);

        $timeStampFields = $this->getTimeStampFields();
        $responseData = array_map(function ($arr) use ($timeStampFields) {
            foreach ($timeStampFields as $field) {
                unset($arr[$field]);
            }
            return $arr;
        }, $filteredData);

        $this->assertEquals(
            count($responseData),
            count($expectedData),
            'Wrong Number of responses returned from filter got: ' . var_export($responseData, true)
        );
        foreach ($expectedData as $i => $data) {
            $prunedData = $this->pruneData($data);
            $this->compareData($prunedData, $responseData[$i]);
        }
    }

    /**
     * Test that a filter returns the expected data
     */
    protected function jsonApiFilterTest(array $filters, array $expectedData, string $jwt): void
    {
        $endpoint = $this->getPluralName();
        $parameters = array_merge([
            'version' => $this->apiVersion,
        ], $filters);
        $this->createJsonApiRequest(
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_{$endpoint}_getall",
                $parameters
            ),
            null,
            $jwt
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
     */
    protected function getFiltered(
        string $endpoint,
        string $responseKey,
        array $filters,
        string $jwt,
        ?string $version = null
    ): array {
        $version = $version ?: $this->apiVersion;
        $parameters = array_merge([
            'version' => $version,
        ], $filters);
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_{$endpoint}_getall",
                $parameters
            ),
            null,
            $jwt
        );

        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_OK);

        return json_decode($response->getContent(), true)[$responseKey];
    }

    /**
     * Get data from the GraphQL API using filter parameters
     */
    protected function getGraphQLFiltered(
        string $idField,
        array $filters,
        string $jwt
    ): array {
        $name = $this->getCamelCasedPluralName();
        $filterParts = [];
        foreach ($filters as $key => $value) {
            $quoter = fn($v) => match (true) {
                $v === null, $v === 'null' => 'null',
                $v === true, $v === 'true' => 'true',
                $v === false, $v === 'false' => 'false',
                is_int($v), is_float($v) => $v,
                is_string($v) => '"' . $v . '"',
                default => throw new Exception("Unable to process GraphQL filter $key: $v"),
            };

            if (is_array($value)) {
                $value = array_map($quoter, $value);
                $filter = '[' . implode(',', $value) . ']';
            } else {
                $filter = $quoter($value);
            }
            $filterParts[] = "$key: $filter";
        }
        $filterString = '(' . implode(',', $filterParts) . ')';

        $this->createGraphQLRequest(
            json_encode([
                'query' => "query { $name$filterString { $idField }}",
            ]),
            $jwt
        );
        $response = $this->kernelBrowser->getResponse();
        $this->assertGraphQLResponse($response);
        $content = json_decode($response->getContent());

        $this->assertObjectHasProperty('data', $content);
        $this->assertObjectNotHasProperty('errors', $content);
        $this->assertIsArray($content->data->{$name});

        return $content->data->{$name};
    }

    /**
     * Test invalid filters
     *
     */
    protected function badFilterTest(array $badFilters, string $jwt): void
    {
        $endpoint = $this->getPluralName();
        $parameters = array_merge([
            'version' => $this->apiVersion,
        ], $badFilters);
        $this->createJsonRequest(
            'GET',
            $this->getUrl(
                $this->kernelBrowser,
                "app_api_{$endpoint}_getall",
                $parameters
            ),
            null,
            $jwt
        );

        $response = $this->kernelBrowser->getResponse();

        $this->assertJsonResponse($response, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    /**
     * Test that updating a related entity updates the timestamp on this one
     */
    protected function relatedTimeStampUpdateTest(
        mixed $id,
        string $relatedEndpoint,
        string $relatedResponseKey,
        array $relatedData,
        string $jwt
    ): void {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $initialState = $this->getOne($endpoint, $responseKey, $id, $jwt);
        sleep(2);
        $this->putOne($relatedEndpoint, $relatedResponseKey, $relatedData['id'], $relatedData, $jwt);
        $currentState = $this->getOne($endpoint, $responseKey, $id, $jwt);
        foreach ($this->getTimeStampFields() as $field) {
            $initialStamp = new DateTime($initialState[$field]);
            $currentStamp = new DateTime($currentState[$field]);

            $diff = $currentStamp->getTimestamp() - $initialStamp->getTimestamp();
            $this->assertTrue(
                $diff > 0,
                'The timestamp has not increased. Original: ' . $initialStamp->format('c') .
                ' Now: ' . $currentStamp->format('c')
            );
        }
    }

    /**
     * Test that creating related data updates a timestamp on this endpoint
     */
    protected function relatedTimeStampPostTest(
        mixed $id,
        string $relatedPluralObjectName,
        string $relatedResponseKey,
        array $relatedPostData,
        string $jwt
    ): void {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $initialState = $this->getOne($endpoint, $responseKey, $id, $jwt);
        sleep(2);
        $this->postMany($relatedPluralObjectName, $relatedResponseKey, [$relatedPostData], $jwt);
        $currentState = $this->getOne($endpoint, $responseKey, $id, $jwt);
        foreach ($this->getTimeStampFields() as $field) {
            $initialStamp = new DateTime($initialState[$field]);
            $currentStamp = new DateTime($currentState[$field]);

            $diff = $currentStamp->getTimestamp() - $initialStamp->getTimestamp();
            $this->assertTrue(
                $diff > 0,
                'The timestamp has not increased.  Original: ' . $initialStamp->format('c') .
                ' Now: ' . $currentStamp->format('c')
            );
        }
    }

    /**
     * Test that deleting a related entity updates a timestamp on this one
     */
    protected function relatedTimeStampDeleteTest(
        mixed $id,
        string $relatedPluralObjectName,
        mixed $relatedId,
        string $jwt
    ): void {
        $endpoint = $this->getPluralName();
        $responseKey = $this->getCamelCasedPluralName();
        $initialState = $this->getOne($endpoint, $responseKey, $id, $jwt);
        sleep(2);
        $this->deleteOne($relatedPluralObjectName, $relatedId, $jwt);
        $currentState = $this->getOne($endpoint, $responseKey, $id, $jwt);
        foreach ($this->getTimeStampFields() as $field) {
            $initialStamp = new DateTime($initialState[$field]);
            $currentStamp = new DateTime($currentState[$field]);

            $diff = $currentStamp->getTimestamp() - $initialStamp->getTimestamp();
            $this->assertTrue(
                $diff > 0,
                'The timestamp has not increased.  Original: ' . $initialStamp->format('c') .
                ' Now: ' . $currentStamp->format('c')
            );
        }
    }

    protected function pruneData(array $data): array
    {
        return array_filter($data, fn($v) => ! is_null($v));
    }
}
