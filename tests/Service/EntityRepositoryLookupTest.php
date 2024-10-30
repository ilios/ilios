<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\AamcMethod;
use App\Entity\DTO\AamcMethodDTO;
use App\Entity\DTO\VocabularyDTO;
use App\Entity\ProgramYearObjective;
use App\Entity\Vocabulary;
use App\Repository\AamcMethodRepository;
use App\Repository\ProgramYearObjectiveRepository;
use App\Repository\VocabularyRepository;
use App\Service\EntityRepositoryLookup;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * @covers \App\Service\EntityRepositoryLookup
 */
class EntityRepositoryLookupTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected EntityRepositoryLookup $service;

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->service = static::getContainer()->get(EntityRepositoryLookup::class);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->service);
    }

    /**
     * @dataProvider getRepositoryForEndpointProvider
     */
    public function testGetRepositoryForEndpoint(string $endpoint, string $expected): void
    {
        $repository = $this->service->getRepositoryForEndpoint($endpoint);
        $this->assertEquals(
            $expected,
            $repository::class
        );
    }

    /**
     * @dataProvider getManagerForEntityProvider
     */
    public function testGetManagerForEntity(string $entityClass, string $expected): void
    {
        $repository = $this->service->getManagerForEntity($entityClass);
        $this->assertEquals(
            $expected,
            $repository::class
        );
    }

    /**
     * @dataProvider getDtoClassForEndpointProvider
     */
    public function testGetDtoClassForEndpoint(string $endpoint, string $expected): void
    {
        $this->assertEquals(
            $expected,
            $this->service->getDtoClassForEndpoint($endpoint)
        );
    }

    /**
     * @dataProvider getDtoClassForEndpointFailsProvider
     */
    public function testGetDtoClassForEndpointFails(string $endpoint, string $expected): void
    {
        $this->expectExceptionMessage($expected);
        $this->service->getDtoClassForEndpoint($endpoint);
    }

    public static function getRepositoryForEndpointProvider(): array
    {
        return [
            [ 'aamcmethods', AamcMethodRepository::class ],
            [ 'programyearobjectives', ProgramYearObjectiveRepository::class ],
            [ 'vocabularies', VocabularyRepository::class ],
        ];
    }

    public static function getManagerForEntityProvider(): array
    {
        return [
            [ AamcMethod::class, AamcMethodRepository::class ],
            [ ProgramYearObjective::class, ProgramYearObjectiveRepository::class ],
            [ Vocabulary::class, VocabularyRepository::class ],
        ];
    }

    public static function getDtoClassForEndpointProvider(): array
    {
        return [
            [ 'aamcmethods', AamcMethodDTO::class ],
            [ 'vocabularies', VocabularyDTO::class ],
        ];
    }

    public static function getDtoClassForEndpointFailsProvider(): array
    {
        return [
            [ 'servicetokens', "The DTO 'App\Entity\DTO\ServiceTokenDTO' does not exist." ],
        ];
    }
}
