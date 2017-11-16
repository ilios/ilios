<?php
namespace Tests\CoreBundle\Service;

use Ilios\CoreBundle\Entity\Manager\ApplicationConfigManager;
use Ilios\CoreBundle\Service\Config;
use Mockery as m;
use Tests\CoreBundle\TestCase;
use function Stringy\create as s;

/**
 * Class LoggerQueueTest
 * @package Tests\CoreBundle\\Classes
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
        $_SERVER[$envKey] = $value;
        $result = $config->get($key);
        $this->assertEquals($value, $result);
        unset($_SERVER[$envKey]);
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
        $_SERVER[$envKey] = $value;
        $result = $config->get($key);
        $this->assertTrue($result === false);
        unset($_SERVER[$envKey]);
    }

    public function testConvertsStringTrueToBooleanTrue()
    {
        $manager = m::mock(ApplicationConfigManager::class);
        $config = new Config($manager);
        $value = 'true';
        $key = 'random-key-99';
        $envKey = 'ILIOS_' . s($key)->underscored()->toUpperCase();
        $_SERVER[$envKey] = $value;
        $result = $config->get($key);
        $this->assertTrue($result === true);
        unset($_SERVER[$envKey]);
    }

    public function testConvertsStringNullToNullNull()
    {
        $manager = m::mock(ApplicationConfigManager::class);
        $config = new Config($manager);
        $key = 'random-key-99';
        $envKey = 'ILIOS_' . s($key)->underscored()->toUpperCase();
        $_SERVER[$envKey] = 'null';
        $manager->shouldReceive('getValue')->with($key)->once();
        $config->get($key);
    }

    public function testConvertsUpercaseStringFalseToBooleanFalse()
    {
        $manager = m::mock(ApplicationConfigManager::class);
        $config = new Config($manager);
        $value = 'FALSE';
        $key = 'random-key-99';
        $envKey = 'ILIOS_' . s($key)->underscored()->toUpperCase();
        $_SERVER[$envKey] = $value;
        $result = $config->get($key);
        $this->assertTrue($result === false);
        unset($_SERVER[$envKey]);
    }

    public function testConvertsUpercaseStringTrueToBooleanTrue()
    {
        $manager = m::mock(ApplicationConfigManager::class);
        $config = new Config($manager);
        $value = 'TRUE';
        $key = 'random-key-99';
        $envKey = 'ILIOS_' . s($key)->underscored()->toUpperCase();
        $_SERVER[$envKey] = $value;
        $result = $config->get($key);
        $this->assertTrue($result === true);
        unset($_SERVER[$envKey]);
    }

    public function testConvertsUpercaseStringNullToNullNull()
    {
        $manager = m::mock(ApplicationConfigManager::class);
        $config = new Config($manager);
        $key = 'random-key-99';
        $envKey = 'ILIOS_' . s($key)->underscored()->toUpperCase();
        $_SERVER[$envKey] = 'NULL';
        $manager->shouldReceive('getValue')->with($key)->once();
        $config->get($key);
    }
}
