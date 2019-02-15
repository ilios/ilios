<?php

namespace App\Tests\Service;

use App\Classes\LocalCachingFilesystemDecorator;
use App\Service\Config;
use App\Service\FilesystemFactory;
use League\Flysystem\Adapter\Local;
use League\Flysystem\FilesystemInterface;
use Mockery as m;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

class FilesystemFactoryTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /** @var m\Mock */
    private $config;

    /** @var FilesystemFactory */
    private $filesystemFactory;

    public function setUp()
    {
        $this->config = m::mock(Config::class);
        $this->filesystemFactory = new FilesystemFactory($this->config, '/tmp');
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        unset($this->config);
        unset($this->filesystemFactory);
    }

    public function testNoS3GivesLocal()
    {
        $this->config->shouldReceive('get')->with('storage_s3_url')->andReturn(null);
        $this->config->shouldReceive('get')->with('file_system_storage_path')->andReturn('/tmp');
        $result = $this->filesystemFactory->getFilesystem();
        $this->assertInstanceOf(FilesystemInterface::class, $result);
        $this->assertNotInstanceOf(LocalCachingFilesystemDecorator::class, $result);
    }

    public function testGetFilesystemFailsFromBadS3Url()
    {
        $this->expectException(\Exception::class);
        $this->config->shouldReceive('get')->with('storage_s3_url')->andReturn('bad');
        $this->filesystemFactory->getFilesystem();
    }
}
