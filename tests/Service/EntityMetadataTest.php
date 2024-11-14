<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Attributes\DTO;
use App\Attributes\Entity;
use App\Classes\AcademicYear;
use App\Entity\AamcMethod;
use App\Entity\DTO\AamcMethodDTO;
use App\Entity\DTO\CohortDTO;
use App\Entity\DTO\CourseDTO;
use App\Entity\DTO\IlmSessionDTO;
use App\Entity\DTO\VocabularyDTO;
use App\Entity\IngestionException;
use App\Entity\Session;
use App\Entity\Vocabulary;
use App\Service\EntityMetadata;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use ReflectionClass;
use ReflectionProperty;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Symfony\Contracts\Cache\CacheInterface;

#[\PHPUnit\Framework\Attributes\CoversClass(\App\Service\EntityMetadata::class)]
class EntityMetadataTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected CacheInterface $appCache;
    protected EntityMetadata $service;

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->appCache = new NullAdapter();
        $this->service = new EntityMetadata(
            $this->appCache,
            self::$kernel
        );
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->appCache);
        unset($this->service);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('entityProvider')]
    public function testIsAnIliosEntity(object $obj): void
    {
        $this->assertTrue($this->service->isAnIliosEntity($obj));
        $this->assertTrue($this->service->isAnIliosEntity($obj::class));
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('dtoProvider')]
    public function testIsNotAnIliosEntity(object $obj): void
    {
        $this->assertFalse($this->service->isAnIliosEntity($obj));
        $this->assertFalse($this->service->isAnIliosEntity($obj::class));
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('dtoProvider')]
    public function testIsAnIliosDto(object $obj): void
    {
        $this->assertTrue($this->service->isAnIliosDto($obj));
        $this->assertTrue($this->service->isAnIliosDto($obj::class));
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('entityProvider')]
    public function testIsNotAnIliosDto(object $obj): void
    {
        $this->assertFalse($this->service->isAnIliosDto($obj));
        $this->assertFalse($this->service->isAnIliosDto($obj::class));
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('extractExposedPropertiesProvider')]
    public function testExtractExposedProperties(string $class, array $expected): void
    {
        $properties = $this->service->extractExposedProperties(new ReflectionClass($class));
        $this->assertEquals($expected, array_keys($properties));
        foreach ($properties as $property) {
            $this->assertInstanceOf(ReflectionProperty::class, $property);
        }
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('extractFilterableProvider')]
    public function testExtractFilterable(string $class, array $expected): void
    {
        $properties = $this->service->extractFilterable(new ReflectionClass($class));
        $this->assertEquals($expected, $properties);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('extractIdProvider')]
    public function testExtractId(string $class, string $expected): void
    {
        $this->assertEquals(
            $expected,
            $this->service->extractId(new ReflectionClass($class)),
        );
    }
    public function testExtractIdFailsOnMissingAnnotation(): void
    {
        $obj = new stdClass();
        $this->expectExceptionMessage('stdClass has no property annotated with @Id');
        $this->service->extractId(new ReflectionClass($obj));
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('extractRelatedProvider')]
    public function testExtractRelated(string $class, array $expected): void
    {
        $this->assertEquals(
            $expected,
            $this->service->extractRelated(new ReflectionClass($class)),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('extractRelatedNameForPropertyProvider')]
    public function testExtractRelatedNameForProperty(string $class, string $propertyName, string $expected): void
    {
        $reflection = new ReflectionClass($class);
        $reflectionProperty = $reflection->getProperty($propertyName);
        $this->assertEquals(
            $expected,
            $this->service->extractRelatedNameForProperty($reflectionProperty),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('extractTypeProvider')]
    public function testExtractType(string $class, string $expected): void
    {
        $this->assertEquals(
            $expected,
            $this->service->extractType(new ReflectionClass($class)),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('extractWritablePropertiesProvider')]
    public function testExtractWritableProperties(string $class, array $expected): void
    {
        $this->assertEquals(
            $expected,
            array_keys($this->service->extractWritableProperties(new ReflectionClass($class))),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('extractOnlyReadablePropertiesProvider')]
    public function testExtractOnlyReadableProperties(string $class, array $expected): void
    {
        $this->assertEquals(
            $expected,
            array_keys($this->service->extractOnlyReadableProperties(new ReflectionClass($class))),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('getTypeOfPropertyProvider')]
    public function testGetTypeOfProperty(string $class, string $propertyName, string $expected): void
    {
        $reflection = new ReflectionClass($class);
        $reflectionProperty = $reflection->getProperty($propertyName);
        $this->assertEquals(
            $expected,
            $this->service->getTypeOfProperty($reflectionProperty),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('isPropertyOnlyReadableProvider')]
    public function testIsPropertyOnlyReadable(string $class, string $propertyName, bool $expected): void
    {
        $reflection = new ReflectionClass($class);
        $reflectionProperty = $reflection->getProperty($propertyName);
        $this->assertEquals(
            $expected,
            $this->service->isPropertyOnlyReadable($reflectionProperty),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('isPropertyRemoveMarkupProvider')]
    public function testIsPropertyRemoveMarkup(string $class, string $propertyName, bool $expected): void
    {
        $reflection = new ReflectionClass($class);
        $reflectionProperty = $reflection->getProperty($propertyName);
        $this->assertEquals(
            $expected,
            $this->service->isPropertyRemoveMarkup($reflectionProperty),
        );
    }

    public function testGetDtoList(): void
    {
        $dtos = $this->service->getDtoList();
        $this->assertNotEmpty($dtos);
        foreach ($dtos as $dto) {
            $reflection = new ReflectionClass($dto);
            $this->assertNotEmpty($reflection->getAttributes(DTO::class));
        }
    }

    public function testGetEntityList(): void
    {
        $entities = $this->service->getEntityList();
        $this->assertNotEmpty($entities);
        foreach ($entities as $entity) {
            $reflection = new ReflectionClass($entity);
            $this->assertNotEmpty($reflection->getAttributes(Entity::class));
        }
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('getEntityForTypeProvider')]
    public function testGetEntityForType(string $type, string $expected): void
    {
        $this->assertEquals($expected, $this->service->getEntityForType($type));
    }

    public function testGetEntityForTypeFailsOnInvalidType(): void
    {
        $this->expectExceptionMessage('Invalid Type. No DTO for geflarknik');
        $this->service->getEntityForType('geflarknik');
    }

    public static function dtoProvider(): array
    {
        return [
            [new AcademicYear(2024, '2024-2025')],
            [new AamcMethodDTO('001', 'lorem ipsum', true)],
        ];
    }

    public static function entityProvider(): array
    {
        return [
            [new AamcMethod()],
            [new Vocabulary()],
        ];
    }

    public static function extractExposedPropertiesProvider(): array
    {
        return [
            [
                IlmSessionDTO::class,
                [
                    'id',
                    'session',
                    'hours',
                    'dueDate',
                    'learnerGroups',
                    'instructorGroups',
                    'instructors',
                    'learners',
                ],
            ],
            [stdClass::class, []],
        ];
    }

    public static function extractFilterableProvider(): array
    {
        return [
            [
                CohortDTO::class,
                [
                    'schools' => 'array<integer>',
                    'startYears' => 'array<integer>',
                ],
            ],
            [stdClass::class, []],
        ];
    }

    public static function extractIdProvider(): array
    {
        return [
            [AamcMethodDTO::class, 'id'],
            [VocabularyDTO::class, 'id'],
        ];
    }

    public static function extractRelatedProvider(): array
    {
        return [
            [
                VocabularyDTO::class,
                [
                    'school' => 'schools',
                    'terms' => 'terms',
                ],
            ],
            [
                CourseDTO::class,
                [
                    'clerkshipType' => 'courseClerkshipTypes',
                    'ancestor' => 'courses',
                    'directors' => 'users',
                    'administrators' => 'users',
                    'studentAdvisors' => 'users',
                    'cohorts' => 'cohorts',
                    'courseObjectives' => 'courseObjectives',
                    'meshDescriptors' => 'meshDescriptors',
                    'learningMaterials' => 'courseLearningMaterials',
                    'sessions' => 'sessions',
                    'descendants' => 'courses',
                    'school' => 'schools',
                    'terms' => 'terms',
                ],
            ],
        ];
    }

    public static function extractRelatedNameForPropertyProvider(): array
    {
        return [
            [VocabularyDTO::class, 'school', 'schools'],
            [VocabularyDTO::class, 'terms', 'terms'],
        ];
    }

    public static function extractTypeProvider(): array
    {
        return [
            [VocabularyDTO::class, 'vocabularies'],
            [AamcMethodDTO::class, 'aamcMethods'],
        ];
    }

    public static function extractWritablePropertiesProvider(): array
    {
        return [
            [AamcMethod::class, ['id', 'description', 'sessionTypes', 'active']],
            [IngestionException::class, ['uid', 'user']],
            [stdClass::class, []],
        ];
    }

    public static function extractOnlyReadablePropertiesProvider(): array
    {
        return [
            [AamcMethod::class, []],
            [IngestionException::class, ['id']],
            [stdClass::class, []],
        ];
    }

    public static function getTypeOfPropertyProvider(): array
    {
        return [
            [VocabularyDTO::class, 'id', 'integer'],
            [VocabularyDTO::class, 'title', 'string'],
            [VocabularyDTO::class, 'school', 'integer'],
            [VocabularyDTO::class, 'active', 'boolean'],
            [VocabularyDTO::class, 'terms', 'array<integer>'],
        ];
    }

    public static function isPropertyOnlyReadableProvider(): array
    {
        return [
            [IngestionException::class, 'id', true],
            [IngestionException::class, 'uid', false],
            [IngestionException::class, 'user', false],
        ];
    }

    public static function isPropertyRemoveMarkupProvider(): array
    {
        return [
            [Session::class, 'description', true],
            [Session::class, 'title', false],
        ];
    }

    public static function getEntityForTypeProvider(): array
    {
        return [
            ['aamcMethods', AamcMethod::class],
            ['vocabularies', Vocabulary::class],
        ];
    }
}
