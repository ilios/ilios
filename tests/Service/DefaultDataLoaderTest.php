<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\DataimportFileLocator;
use App\Service\DefaultDataImporter;
use App\Service\DefaultDataLoader;
use App\Tests\TestCase;
use Mockery as m;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;

/**
 * @package App\Tests\Service
 * @covers \App\Service\DefaultDataLoader
 */
class DefaultDataLoaderTest extends TestCase
{
    protected string $projectRootDir;

    public function setUp(): void
    {
        parent::setUp();
        // @todo figure out how to set this from the $kernelRootDir variable. [ST 2021/08/04]
        $this->projectRootDir = dirname(__DIR__, 2);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->projectRootDir);
    }

    /**
     * @covers ::load()
     */
    public function testImport()
    {
        $defaultDataLoader = new DefaultDataLoader(new DataimportFileLocator($this->projectRootDir));
        $assessmentOptions = $defaultDataLoader->load(DefaultDataImporter::ASSESSMENT_OPTION);
        $this->assertCount(2, $assessmentOptions);
    }

    /**
     * @covers ::load()
     */
    public function testImportFailsIfFileNotFound()
    {
        $this->expectException(FileLocatorFileNotFoundException::class);
        $defaultDataLoader = new DefaultDataLoader(new DataimportFileLocator($this->projectRootDir));
        $defaultDataLoader->load('gefkarknik');
    }
}
