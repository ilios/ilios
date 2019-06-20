<?php
namespace App\Tests\Service;

use App\Entity\Manager\ApplicationConfigManager;
use App\Service\Config;
use Mockery as m;
use App\Tests\TestCase;
use function Stringy\create as s;

/**
 * Class LoggerQueueTest
 * @package App\Tests\Classes
 */
class ConfigTest extends TestCase
{
    public function testPullsFromENVFirst()
    {
        $manager = m::mock(ApplicationConfigManager::class);
        $config = new Config($manager);
        $value = '123Test';
        $key = 'random-key-99';
        $envKey = 'ILIOS_' . s($key)->underscored()->toUpperCase();
        $_ENV[$envKey] = $value;
        $result = $config->get($key);
        $this->assertEquals($value, $result);
        unset($_ENV[$envKey]);
    }

    public function testPullsFromDBIfNoEnv()
    {
        $manager = m::mock(ApplicationConfigManager::class);
        $config = new Config($manager);
        $value = '123Test';
        $key = 'random-key-99';
        $manager->shouldReceive('getValue')->with($key)->once()->andReturn($value);
        $result = $config->get($key);
        $this->assertEquals($value, $result);
    }

    public function testConvertsStringFalseToBooleanFalse()
    {
        $manager = m::mock(ApplicationConfigManager::class);
        $config = new Config($manager);
        $value = 'false';
        $key = 'random-key-99';
        $envKey = 'ILIOS_' . s($key)->underscored()->toUpperCase();
        $_ENV[$envKey] = $value;
        $result = $config->get($key);
        $this->assertTrue($result === false);
        unset($_ENV[$envKey]);
    }

    public function testConvertsStringTrueToBooleanTrue()
    {
        $manager = m::mock(ApplicationConfigManager::class);
        $config = new Config($manager);
        $value = 'true';
        $key = 'random-key-99';
        $envKey = 'ILIOS_' . s($key)->underscored()->toUpperCase();
        $_ENV[$envKey] = $value;
        $result = $config->get($key);
        $this->assertTrue($result === true);
        unset($_ENV[$envKey]);
    }

    public function testConvertsStringNullToNullNull()
    {
        $manager = m::mock(ApplicationConfigManager::class);
        $config = new Config($manager);
        $key = 'random-key-99';
        $envKey = 'ILIOS_' . s($key)->underscored()->toUpperCase();
        $_ENV[$envKey] = 'null';
        $manager->shouldReceive('getValue')->with($key)->once();
        $config->get($key);
        unset($_ENV[$envKey]);
    }

    public function testConvertsUppercaseStringFalseToBooleanFalse()
    {
        $manager = m::mock(ApplicationConfigManager::class);
        $config = new Config($manager);
        $value = 'FALSE';
        $key = 'random-key-99';
        $envKey = 'ILIOS_' . s($key)->underscored()->toUpperCase();
        $_ENV[$envKey] = $value;
        $result = $config->get($key);
        $this->assertTrue($result === false);
        unset($_ENV[$envKey]);
    }

    public function testConvertsUppercaseStringTrueToBooleanTrue()
    {
        $manager = m::mock(ApplicationConfigManager::class);
        $config = new Config($manager);
        $value = 'TRUE';
        $key = 'random-key-99';
        $envKey = 'ILIOS_' . s($key)->underscored()->toUpperCase();
        $_ENV[$envKey] = $value;
        $result = $config->get($key);
        $this->assertTrue($result === true);
        unset($_ENV[$envKey]);
    }

    public function testConvertsUppercaseStringNullToNullNull()
    {
        $manager = m::mock(ApplicationConfigManager::class);
        $config = new Config($manager);
        $key = 'random-key-99';
        $envKey = 'ILIOS_' . s($key)->underscored()->toUpperCase();
        $_ENV[$envKey] = 'NULL';
        $manager->shouldReceive('getValue')->with($key)->once();
        $config->get($key);
        unset($_ENV[$envKey]);
    }

    public function testDoesNotOverwriteEnvWithServer()
    {
        $manager = m::mock(ApplicationConfigManager::class);
        $config = new Config($manager);
        $value = '123Test';
        $key = 'random-key-99';
        $envKey = 'ILIOS_' . s($key)->underscored()->toUpperCase();
        $_ENV[$envKey] = $value;
        $_SERVER[$envKey] = 'bad';
        $result = $config->get($key);
        $this->assertEquals($value, $result);
        unset($_SERVER[$envKey]);
        unset($_ENV[$envKey]);
    }

    public function testLooksInServerIfEnvIsNull()
    {
        $manager = m::mock(ApplicationConfigManager::class);
        $config = new Config($manager);
        $value = '123Test';
        $key = 'random-key-99';
        $envKey = 'ILIOS_' . s($key)->underscored()->toUpperCase();
        $_ENV[$envKey] = null;
        $_SERVER[$envKey] = $value;
        $result = $config->get($key);
        $this->assertEquals($value, $result);
        unset($_SERVER[$envKey]);
        unset($_ENV[$envKey]);
    }

    public function testLooksInServerIfEnvIsNotSet()
    {
        $manager = m::mock(ApplicationConfigManager::class);
        $config = new Config($manager);
        $value = '123Test';
        $key = 'random-key-99';
        $envKey = 'ILIOS_' . s($key)->underscored()->toUpperCase();
        $_SERVER[$envKey] = $value;
        $result = $config->get($key);
        $this->assertEquals($value, $result);
        unset($_SERVER[$envKey]);
    }
}
