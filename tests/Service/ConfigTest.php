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
class ConfigTest extends TestCase
{
    public function testPullsFromENVFirst()
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

    public function testPullsFromDBIfNoEnv()
    {
        $repository = m::mock(ApplicationConfigRepository::class);
        $config = new Config($repository);
        $value = '123Test';
        $key = 'random-key-99';
        $repository->shouldReceive('getValue')->with($key)->once()->andReturn($value);
        $result = $config->get($key);
        $this->assertEquals($value, $result);
    }

    public function testConvertsStringFalseToBooleanFalse()
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

    public function testConvertsStringTrueToBooleanTrue()
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

    public function testConvertsStringNullToNullNull()
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

    public function testConvertsUppercaseStringFalseToBooleanFalse()
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

    public function testConvertsUppercaseStringTrueToBooleanTrue()
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

    public function testConvertsUppercaseStringNullToNullNull()
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

    public function testDoesNotOverwriteEnvWithServer()
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

    public function testLooksInServerIfEnvIsNull()
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

    public function testLooksInServerIfEnvIsNotSet()
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
}
