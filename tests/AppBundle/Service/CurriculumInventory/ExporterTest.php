<?php
namespace Tests\AppBundle\Service\CurriculumInventory;

use AppBundle\Service\CurriculumInventory\Export\Aggregator;
use AppBundle\Service\CurriculumInventory\Export\XmlPrinter;
use AppBundle\Service\CurriculumInventory\Exporter;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

use Mockery as m;

/**
 * Class ExporterTest
 */
class ExporterTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @covers \AppBundle\Service\CurriculumInventory\Exporter::__construct
     */
    public function testConstructor()
    {
        $aggregator = m::mock(Aggregator::class);
        $printer = m::mock(XmlPrinter::class);
        $exporter = new Exporter($aggregator, $printer);

        $this->assertTrue($exporter instanceof Exporter);
    }
}
