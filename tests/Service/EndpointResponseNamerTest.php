<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Entity\AamcMethod;
use App\Service\EndpointResponseNamer;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

#[\PHPUnit\Framework\Attributes\CoversClass(\App\Service\EndpointResponseNamer::class)]
class EndpointResponseNamerTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected EndpointResponseNamer $service;

    public function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        $this->service = static::getContainer()->get(EndpointResponseNamer::class);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->service);
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('getSingularNameProvider')]
    public function testGetSingularName(string $endpointKey, string $expected): void
    {
        $this->assertEquals(
            $expected,
            $this->service->getSingularName($endpointKey),
        );
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('getPluralNameProvider')]
    public function testGetPluralName(string $endpointKey, string $expected): void
    {
        $this->assertEquals(
            $expected,
            $this->service->getPluralName($endpointKey),
        );
    }

    public static function getSingularNameProvider(): array
    {
        return [
            [ 'aamcmethods', 'aamcMethod' ],
            [ 'aamcpcrses', 'aamcPcrs' ],
            [ 'vocabularies', 'vocabulary' ],
        ];
    }

    public static function getPluralNameProvider(): array
    {
        return [
            [ 'aamcmethods', 'aamcMethods' ],
            [ 'aamcpcrses', 'aamcPcrses' ],
            [ 'vocabularies', 'vocabularies' ],
        ];
    }
}
