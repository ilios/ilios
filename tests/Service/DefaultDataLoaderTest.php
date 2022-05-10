<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\DataimportFileLocator;
use App\Service\DefaultDataImporter;
use App\Service\DefaultDataLoader;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Config\Exception\FileLocatorFileNotFoundException;

/**
 * @package App\Tests\Service
 * @coversDefaultClass \App\Service\DefaultDataLoader
 */
class DefaultDataLoaderTest extends KernelTestCase
{
    protected string $projectRootDir;

    public function setUp(): void
    {
        parent::setUp();
        $kernel = self::bootKernel();
        $this->projectRootDir = $kernel->getProjectDir();
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->projectRootDir);
    }

    /**
     * @covers ::load
     */
    public function testImport()
    {
        $defaultDataLoader = new DefaultDataLoader(new DataimportFileLocator($this->projectRootDir));
        $assessmentOptions = $defaultDataLoader->load(DefaultDataImporter::ASSESSMENT_OPTION);
        $this->assertCount(2, $assessmentOptions);
    }

    /**
     * @covers ::load
     */
    public function testImportFailsIfFileNotFound()
    {
        $this->expectException(FileLocatorFileNotFoundException::class);
        $defaultDataLoader = new DefaultDataLoader(new DataimportFileLocator($this->projectRootDir));
        $defaultDataLoader->load('gefkarknik');
    }
}
