<?php
namespace Tests\AppBundle\Service;

use AppBundle\Entity\Manager\ApplicationConfigManager;
use AppBundle\Service\Config;
use Mockery as m;
use Tests\AppBundle\TestCase;
use function Stringy\create as s;

/**
 * Class LoggerQueueTest
 * @package Tests\AppBundle\Classes
 */
class ConfigTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

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

    public function testConvertsUpercaseStringFalseToBooleanFalse()
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

    public function testConvertsUpercaseStringTrueToBooleanTrue()
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

    public function testConvertsUpercaseStringNullToNullNull()
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

    public function testLooksInServerIfNotInEnv()
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
}
