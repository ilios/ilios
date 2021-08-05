<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Repository\DataImportRepositoryInterface;
use App\Service\DefaultDataImporter;
use App\Service\DefaultDataLoader;
use App\Tests\TestCase;
use Mockery as m;

/**
 * @package App\Tests\Service
 * @covers \App\Service\DefaultDataImporter
 */
class DefaultDataImporterTest extends TestCase
{
    /**
     * @covers ::import()
     */
    public function testImport()
    {
        $repository = m::mock(DataImportRepositoryInterface::class);
        $loader = m::mock(DefaultDataLoader::class);
        $importer = new DefaultDataImporter($loader);
        $type = 'foo';
        $referenceMap = [];
        $records = [
            [1, 'bar'],
            [2, 'baz'],
        ];
        $loader->shouldReceive('load')->withArgs([$type])->andReturn($records);
        $repository->shouldReceive('import')->withArgs([$records[0], $type, $referenceMap])->andReturn($referenceMap);
        $repository->shouldReceive('import')->withArgs([$records[1], $type, $referenceMap])->andReturn($referenceMap);
        $rhett = $importer->import($repository, $type, $referenceMap);
        $this->assertSame($rhett, $referenceMap);
    }
}
