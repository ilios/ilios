<?php

declare(strict_types=1);

namespace App\Tests\Service\CurriculumInventory;

use PHPUnit\Framework\Attributes\CoversClass;
use App\Entity\CurriculumInventoryReport;
use App\Service\CurriculumInventory\Export\Aggregator;
use App\Service\CurriculumInventory\Export\XmlPrinter;
use App\Service\CurriculumInventory\Exporter;
use App\Tests\TestCase;
use Mockery as m;

/**
 * Class ExporterTest
 */
#[CoversClass(Exporter::class)]
class ExporterTest extends TestCase
{
    public function testPrint(): void
    {
        $report = new CurriculumInventoryReport();
        $data = ['whatever'];
        $out = 'essbesteck';
        $aggregator = m::mock(Aggregator::class);
        $aggregator->shouldReceive('getData')->with($report)->andReturn($data);
        $printer = m::mock(XmlPrinter::class);
        $printer->shouldReceive('print')->with($data)->andReturn($out);
        $exporter = new Exporter($aggregator, $printer);
        $this->assertEquals($out, $exporter->getXmlReport($report));
    }
}
