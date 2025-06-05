<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Classes\LocalCachingFilesystemDecorator;
use App\Service\Config;
use App\Service\FilesystemFactory;
use App\Tests\TestCase;
use Exception;
use League\Flysystem\FilesystemOperator;
use Mockery as m;

final class FilesystemFactoryTest extends TestCase
{
    private m\MockInterface|Config $config;
    private FilesystemFactory $filesystemFactory;

    public function setUp(): void
    {
        parent::setUp();
        $this->config = m::mock(Config::class);
        $this->filesystemFactory = new FilesystemFactory($this->config, '/tmp');
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->config);
        unset($this->filesystemFactory);
    }

    public function testNoS3GivesLocal(): void
    {
        $this->config->shouldReceive('get')->with('storage_s3_url')->andReturn(null);
        $this->config->shouldReceive('get')->with('file_system_storage_path')->andReturn('/tmp');
        $result = $this->filesystemFactory->getFilesystem();
        $this->assertInstanceOf(FilesystemOperator::class, $result);
        $this->assertNotInstanceOf(LocalCachingFilesystemDecorator::class, $result);
    }

    public function testGetFilesystemFailsFromBadS3Url(): void
    {
        $this->expectException(Exception::class);
        $this->config->shouldReceive('get')->with('storage_s3_url')->andReturn('bad');
        $this->filesystemFactory->getFilesystem();
    }
}
