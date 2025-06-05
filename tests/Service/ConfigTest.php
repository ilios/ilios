<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Repository\ApplicationConfigRepository;
use App\Service\Config;
use Mockery as m;
use App\Tests\TestCase;

/**
 * Class LoggerQueueTest
 * @package App\Tests\Classes
 */
final class ConfigTest extends TestCase
{
    public function testPullsFromENVFirst(): void
    {
        $repository = m::mock(ApplicationConfigRepository::class);
        $config = new Config($repository);
        $value = '123Test';
        $key = 'random-key-99';
        $envKey = 'ILIOS_RANDOM_KEY_99';
        $_ENV[$envKey] = $value;
        $result = $config->get($key);
        $this->assertEquals($value, $result);
        unset($_ENV[$envKey]);
    }

    public function testPullsFromDBIfNoEnv(): void
    {
        $repository = m::mock(ApplicationConfigRepository::class);
        $config = new Config($repository);
        $value = '123Test';
        $key = 'random-key-99';
        $repository->shouldReceive('getValue')->with($key)->once()->andReturn($value);
        $result = $config->get($key);
        $this->assertEquals($value, $result);
    }

    public function testConvertsStringFalseToBooleanFalse(): void
    {
        $repository = m::mock(ApplicationConfigRepository::class);
        $config = new Config($repository);
        $value = 'false';
        $key = 'random-key-99';
        $envKey = 'ILIOS_RANDOM_KEY_99';
        $_ENV[$envKey] = $value;
        $result = $config->get($key);
        $this->assertTrue($result === false);
        unset($_ENV[$envKey]);
    }

    public function testConvertsStringTrueToBooleanTrue(): void
    {
        $repository = m::mock(ApplicationConfigRepository::class);
        $config = new Config($repository);
        $value = 'true';
        $key = 'random-key-99';
        $envKey = 'ILIOS_RANDOM_KEY_99';
        $_ENV[$envKey] = $value;
        $result = $config->get($key);
        $this->assertTrue($result === true);
        unset($_ENV[$envKey]);
    }

    public function testConvertsStringNullToNullNull(): void
    {
        $repository = m::mock(ApplicationConfigRepository::class);
        $config = new Config($repository);
        $key = 'random-key-99';
        $envKey = 'ILIOS_RANDOM_KEY_99';
        $_ENV[$envKey] = 'null';
        $repository->shouldReceive('getValue')->with($key)->once();
        $config->get($key);
        unset($_ENV[$envKey]);
    }

    public function testConvertsUppercaseStringFalseToBooleanFalse(): void
    {
        $repository = m::mock(ApplicationConfigRepository::class);
        $config = new Config($repository);
        $value = 'FALSE';
        $key = 'random-key-99';
        $envKey = 'ILIOS_RANDOM_KEY_99';
        $_ENV[$envKey] = $value;
        $result = $config->get($key);
        $this->assertTrue($result === false);
        unset($_ENV[$envKey]);
    }

    public function testConvertsUppercaseStringTrueToBooleanTrue(): void
    {
        $repository = m::mock(ApplicationConfigRepository::class);
        $config = new Config($repository);
        $value = 'TRUE';
        $key = 'random-key-99';
        $envKey = 'ILIOS_RANDOM_KEY_99';
        $_ENV[$envKey] = $value;
        $result = $config->get($key);
        $this->assertTrue($result === true);
        unset($_ENV[$envKey]);
    }

    public function testConvertsUppercaseStringNullToNullNull(): void
    {
        $repository = m::mock(ApplicationConfigRepository::class);
        $config = new Config($repository);
        $key = 'random-key-99';
        $envKey = 'ILIOS_RANDOM_KEY_99';
        $_ENV[$envKey] = 'NULL';
        $repository->shouldReceive('getValue')->with($key)->once();
        $config->get($key);
        unset($_ENV[$envKey]);
    }

    public function testDoesNotOverwriteEnvWithServer(): void
    {
        $repository = m::mock(ApplicationConfigRepository::class);
        $config = new Config($repository);
        $value = '123Test';
        $key = 'random-key-99';
        $envKey = 'ILIOS_RANDOM_KEY_99';
        $_ENV[$envKey] = $value;
        $_SERVER[$envKey] = 'bad';
        $result = $config->get($key);
        $this->assertEquals($value, $result);
        unset($_SERVER[$envKey]);
        unset($_ENV[$envKey]);
    }

    public function testLooksInServerIfEnvIsNull(): void
    {
        $repository = m::mock(ApplicationConfigRepository::class);
        $config = new Config($repository);
        $value = '123Test';
        $key = 'random-key-99';
        $envKey = 'ILIOS_RANDOM_KEY_99';
        $_ENV[$envKey] = null;
        $_SERVER[$envKey] = $value;
        $result = $config->get($key);
        $this->assertEquals($value, $result);
        unset($_SERVER[$envKey]);
        unset($_ENV[$envKey]);
    }

    public function testLooksInServerIfEnvIsNotSet(): void
    {
        $repository = m::mock(ApplicationConfigRepository::class);
        $config = new Config($repository);
        $value = '123Test';
        $key = 'random-key-99';
        $envKey = 'ILIOS_RANDOM_KEY_99';
        $_SERVER[$envKey] = $value;
        $result = $config->get($key);
        $this->assertEquals($value, $result);
        unset($_SERVER[$envKey]);
    }

    public function testConvertsCASString3ToInt3(): void
    {
        $repository = m::mock(ApplicationConfigRepository::class);
        $config = new Config($repository);
        $envKey = 'ILIOS_CAS_AUTHENTICATION_VERSION';
        $_ENV[$envKey] = '3';
        $result = $config->get('cas_authentication_version');
        $this->assertIsInt($result);
        $this->assertEquals(3, $result);
        unset($_ENV[$envKey]);
    }

    public function testConvertsCASString3ToInt3DB(): void
    {
        $repository = m::mock(ApplicationConfigRepository::class);
        $config = new Config($repository);
        $key = 'cas_authentication_version';
        $repository->shouldReceive('getValue')->with($key)->once()->andReturn('3');
        $result = $config->get($key);
        $this->assertIsInt($result);
        $this->assertEquals(3, $result);
    }

    public function testCASAuthEmptyNull(): void
    {
        $repository = m::mock(ApplicationConfigRepository::class);
        $config = new Config($repository);
        $key = 'cas_authentication_version';
        $repository->shouldReceive('getValue')->with($key)->once()->andReturn('');
        $result = $config->get($key);
        $this->assertEquals(null, $result);
    }
}
