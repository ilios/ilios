<?php
namespace Tests\CoreBundle\Service;

use Ilios\CoreBundle\Entity\Manager\ApplicationConfigManager;
use Ilios\CoreBundle\Service\Config;
use Mockery as m;
use Tests\CoreBundle\TestCase;

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
        $envKey = 'ILIOS_' . strtoupper($key);
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
}
