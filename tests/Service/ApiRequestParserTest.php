<?php

declare(strict_types=1);

namespace App\Tests\Service;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Entity\AamcMethod;
use App\Entity\AamcMethodInterface;
use App\Entity\SessionType;
use App\Service\ApiRequestParser;
use App\Tests\Fixture\LoadAamcMethodData;
use App\Tests\Fixture\LoadSchoolData;
use App\Tests\Fixture\LoadSessionTypeData;
use Closure;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;

#[CoversClass(ApiRequestParser::class)]
final class ApiRequestParserTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected ApiRequestParser $service;

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $databaseToolCollection = self::getContainer()->get(DatabaseToolCollection::class);
        $databaseTool = $databaseToolCollection->get();
        $databaseTool->loadFixtures([
            LoadAamcMethodData::class,
            LoadSessionTypeData::class,
        ]);
        $this->service = static::getContainer()->get(ApiRequestParser::class);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->service);
    }

    #[DataProvider('extractParametersProvider')]
    public function testExtractParameters(array $parameters, array $expected): void
    {
        $this->assertEquals(
            $expected,
            ApiRequestParser::extractParameters(Request::create('/foobar', Request::METHOD_GET, $parameters)),
        );
    }

    #[DataProvider('extractPostDataFromRequestProvider')]
    public function testExtractPostDataFromRequest(
        bool $isJsonApi,
        array $data,
        string $object,
        array $expected
    ): void {
        $headers = $isJsonApi ? [ 'HTTP_ACCEPT' => 'application/vnd.api+json' ] : [];

        $request = Request::create(
            '/foobar',
            method: Request::METHOD_POST,
            server: $headers,
            content: json_encode($data),
        );
        $extractedData = $this->service->extractPostDataFromRequest($request, $object);
        $this->assertEquals($expected, $extractedData);
    }

    #[DataProvider('extractPostDataFromRequestFailsOnBadDataProvider')]
    public function testExtractPostDataFromRequestFailsOnBadData(
        bool $isJsonApi,
        mixed $data,
        string $object,
        string $expected
    ): void {
        $this->expectExceptionMessage($expected);

        $headers = $isJsonApi ? [ 'HTTP_ACCEPT' => 'application/vnd.api+json' ] : [];

        $request = Request::create(
            '/foobar',
            method: Request::METHOD_POST,
            server: $headers,
            content: json_encode($data),
        );
        $this->service->extractPostDataFromRequest($request, $object);
    }

    #[DataProvider('extractPutDataFromRequestProvider')]
    public function testExtractPutDataFromRequest(
        bool $isJsonApi,
        array $data,
        string $object,
        object $expected
    ): void {
        $headers = $isJsonApi ? [ 'HTTP_ACCEPT' => 'application/vnd.api+json' ] : [];

        $request = Request::create(
            '/foobar',
            method: Request::METHOD_PUT,
            server: $headers,
            content: json_encode($data),
        );
        $extractedData = $this->service->extractPutDataFromRequest($request, $object);
        $this->assertEquals($expected, $extractedData);
    }

    #[DataProvider('extractPutDataFromRequestFailsOnBadDataProvider')]
    public function testExtractPutDataFromRequestFailsOnBadData(
        bool $isJsonApi,
        mixed $data,
        string $object,
        string $expected
    ): void {
        $this->expectExceptionMessage($expected);

        $headers = $isJsonApi ? [ 'HTTP_ACCEPT' => 'application/vnd.api+json' ] : [];

        $request = Request::create(
            '/foobar',
            method: Request::METHOD_PUT,
            server: $headers,
            content: json_encode($data),
        );
        $this->service->extractPutDataFromRequest($request, $object);
    }

    #[DataProvider('extractEntitiesFromPostRequestProvider')]
    public function testExtractEntitiesFromPostRequest(
        bool $isJsonApi,
        array $data,
        string $class,
        string $object,
        Closure $callback,
    ): void {
        $headers = $isJsonApi ? [ 'HTTP_ACCEPT' => 'application/vnd.api+json' ] : [];

        $request = Request::create(
            '/foobar',
            method: Request::METHOD_PUT,
            server: $headers,
            content: json_encode($data),
        );
        $entities = $this->service->extractEntitiesFromPostRequest($request, $class, $object);
        $this->assertCount(1, $entities);
        $entity = $entities[0];
        $callback($entity);
    }

    #[DataProvider('extractEntityFromPutRequestProvider')]
    public function testExtractEntityFromPutRequest(
        bool $isJsonApi,
        array $data,
        string $object,
        object $entity,
        Closure $callback,
    ): void {
        $headers = $isJsonApi ? [ 'HTTP_ACCEPT' => 'application/vnd.api+json' ] : [];

        $request = Request::create(
            '/foobar',
            method: Request::METHOD_PUT,
            server: $headers,
            content: json_encode($data),
        );
        $entity = $this->service->extractEntityFromPutRequest($request, $entity, $object);
        $callback($entity);
    }

    #[DataProvider('extractJsonApiPatchDataFromRequestProvider')]
    public function testExtractJsonApiPatchDataFromRequest(array $data, array $expected): void
    {
        $request = Request::create(
            '/foobar',
            method: Request::METHOD_PATCH,
            content: json_encode($data),
        );
        $extractedData = $this->service->extractJsonApiPatchDataFromRequest($request);
        $this->assertEquals($expected, $extractedData);
    }

    public static function extractParametersProvider(): array
    {
        return [
            [
                [],
                ['offset' => null, 'limit' => null, 'orderBy' => null, 'criteria' => []],
            ],
            [
                ['offset' => '219', 'limit' => '120', 'order_by' => 'foobar'],
                ['offset' => 219, 'limit' => 120, 'orderBy' => 'foobar', 'criteria' => []],
            ],
            [
                [
                    'offset' => '1',
                    'limit' => '2',
                    'order_by' => 'a',
                    'filters' => [
                        'archived' => 'true',
                        'enabled' => 'false',
                        'parent' => 'null',
                        'roles' => [
                            '1',
                            '2',
                        ],
                    ],
                ],
                [
                    'offset' => 1,
                    'limit' => 2,
                    'orderBy' => 'a',
                    'criteria' => [
                        'archived' => true,
                        'enabled' => false,
                        'parent' => null,
                        'roles' => ['1', '2'],
                    ],
                ],
            ],
        ];
    }

    public static function extractPostDataFromRequestProvider(): array
    {
        return [
            [
                false,
                [
                    'aamcMethod' => [
                        'description' => 'some words',
                        'sessionTypes' => ['1', '2'],
                        'active' => true,
                    ],
                ],
                'aamcmethods',
                [
                    (object)[
                        'description' => 'some words',
                        'sessionTypes' => ['1', '2'],
                        'active' => true,
                    ],
                ],
            ],
            [
                true,
                [
                    'data' => [
                        'type' => 'aamcMethods',
                        'attributes' => [
                            'description' => 'some words',
                            'active' => false,
                        ],
                        'relationships' => [
                            'sessionTypes' => [
                                'data' => [
                                    ['type' => 'sessionTypes', 'id' => '1'],
                                    ['type' => 'sessionTypes', 'id' => '2'],
                                ],
                            ],
                        ],
                    ],
                ],
                'aamcmethods',
                [
                    (object)[
                        'description' => 'some words',
                        'sessionTypes' => ['1', '2'],
                        'active' => false,
                    ],
                ],
            ],
        ];
    }

    public static function extractPostDataFromRequestFailsOnBadDataProvider(): array
    {
        return [
            [
                true,
                new stdClass(),
                'does not matter',
                "The required 'data' value was not found in request.",
            ],
            [
                false,
                ['aamcMethod' => []],
                'aamcmethods',
                'Data under the singular key aamcMethod should be an object not an array.',
            ],
            [
                false,
                ['aamcMethods' => ''],
                'aamcmethods',
                'Data under the plural key aamcMethod should be an array not an object.',
            ],

        ];
    }

    public static function extractPutDataFromRequestProvider(): array
    {
        return [
            [
                false,
                [
                    'aamcMethod' => [
                        'id' => 'AM001',
                        'description' => 'some words',
                        'sessionTypes' => ['1', '2'],
                        'active' => true,
                    ],
                ],
                'aamcmethods',
                (object)[
                    'id' => 'AM001',
                    'description' => 'some words',
                    'sessionTypes' => ['1', '2'],
                    'active' => true,
                ],
            ],
            [
                true,
                [
                    'data' => [
                        'type' => 'aamcMethods',
                        'attributes' => [
                            'id' => 'AM001',
                            'description' => 'some words',
                            'active' => true,
                        ],
                        'relationships' => [
                            'sessionTypes' => [
                                'data' => [
                                    ['type' => 'sessionTypes', 'id' => '1'],
                                    ['type' => 'sessionTypes', 'id' => '2'],
                                ],
                            ],
                        ],
                    ],
                ],
                'aamcmethods',
                (object)[
                    'id' => 'AM001',
                    'description' => 'some words',
                    'sessionTypes' => ['1', '2'],
                    'active' => true,
                ],
            ],
        ];
    }

    public static function extractPutDataFromRequestFailsOnBadDataProvider(): array
    {
        return [
            [
                true,
                new stdClass(),
                'does not matter',
                "The required 'data' value was not found in request.",
            ],
            [
                false,
                ['aamcMethod' => []],
                'aamcmethods',
                'Data was found in aamcMethod but it should be an object not an array.',
            ],
            [
                false,
                ['geflarknik' => []],
                'aamcmethods',
                'This request contained no usable data.  Expected to find it under aamcMethod',
            ],

        ];
    }

    public static function extractEntitiesFromPostRequestProvider(): array
    {
        $callback = function (AamcMethodInterface $entity): void {
            self::assertInstanceOf(AamcMethodInterface::class, $entity);
            self::assertEquals('some words', $entity->getDescription());
            self::assertTrue($entity->isActive());
            $sessionTypes = $entity->getSessionTypes()->toArray();
            self::assertCount(2, $sessionTypes);
            self::assertInstanceOf(SessionType::class, $sessionTypes[0]);
            self::assertEquals(1, $sessionTypes[0]->getId());
            self::assertEquals(2, $sessionTypes[1]->getId());
        };

        return [
            [
                true,
                [
                    'data' => [
                        'type' => 'aamcMethods',
                        'attributes' => [
                            'description' => 'some words',
                            'active' => true,
                        ],
                        'relationships' => [
                            'sessionTypes' => [
                                'data' => [
                                    ['type' => 'sessionTypes', 'id' => '1'],
                                    ['type' => 'sessionTypes', 'id' => '2'],
                                ],
                            ],
                        ],
                    ],
                ],
                AamcMethod::class . '[]',
                'does not matter',
                $callback,
            ],
            [
                false,
                [
                    'aamcMethod' => [
                        'description' => 'some words',
                        'sessionTypes' => ['1', '2'],
                        'active' => true,
                    ],
                ],
                AamcMethod::class . '[]',
                'aamcmethods',
                $callback,
            ],
        ];
    }

    public static function extractEntityFromPutRequestProvider(): array
    {
        $callback = function (AamcMethodInterface $entity): void {
            self::assertInstanceOf(AamcMethodInterface::class, $entity);
            self::assertEquals(1, $entity->getId());
            self::assertEquals('some words', $entity->getDescription());
            self::assertTrue($entity->isActive());
            $sessionTypes = $entity->getSessionTypes()->toArray();
            self::assertCount(2, $sessionTypes);
            self::assertInstanceOf(SessionType::class, $sessionTypes[0]);
            self::assertEquals(1, $sessionTypes[0]->getId());
            self::assertEquals(2, $sessionTypes[1]->getId());
        };

        return [
            [
                true,
                [
                    'data' => [
                        'id' => '1',
                        'type' => 'aamcMethods',
                        'attributes' => [
                            'description' => 'some words',
                            'active' => true,
                        ],
                        'relationships' => [
                            'sessionTypes' => [
                                'data' => [
                                    ['type' => 'sessionTypes', 'id' => '1'],
                                    ['type' => 'sessionTypes', 'id' => '2'],
                                ],
                            ],
                        ],
                    ],
                ],
                'does not matter',
                new AamcMethod(),
                $callback,
            ],
            [
                false,
                [
                    'aamcMethod' => [
                        'id' => '1',
                        'description' => 'some words',
                        'sessionTypes' => ['1', '2'],
                        'active' => true,
                    ],
                ],
                'aamcmethods',
                new AamcMethod(),
                $callback,
            ],
        ];
    }

    public static function extractJsonApiPatchDataFromRequestProvider(): array
    {
        return [
            [
                [
                    'data' => [
                        'type' => 'aamcMethods',
                        'attributes' => [
                            'id' => 'AM001',
                            'description' => 'some words',
                            'active' => true,
                        ],
                        'relationships' => [
                            'sessionTypes' => [
                                'data' => [
                                    ['type' => 'sessionTypes', 'id' => '1'],
                                    ['type' => 'sessionTypes', 'id' => '2'],
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'id' => 'AM001',
                    'description' => 'some words',
                    'active' => true,
                    'sessionTypes' => [ '1', '2' ],
                ],
            ],
        ];
    }
}
