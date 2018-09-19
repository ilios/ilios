<?php
namespace Tests\App\Service\CurriculumInventory;

use App\Service\CurriculumInventory\Export\Aggregator;
use App\Service\CurriculumInventory\Export\XmlPrinter;
use App\Service\CurriculumInventory\Exporter;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

use Mockery as m;

/**
 * Class ExporterTest
 */
class ExporterTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @covers \App\Service\CurriculumInventory\Exporter::__construct
     */
    public function testConstructor()
    {
        $aggregator = m::mock(Aggregator::class);
        $printer = m::mock(XmlPrinter::class);
        $exporter = new Exporter($aggregator, $printer);

        $this->assertTrue($exporter instanceof Exporter);
    }
}
