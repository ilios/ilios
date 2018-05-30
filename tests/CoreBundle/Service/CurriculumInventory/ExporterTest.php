<?php
namespace Tests\CoreBundle\Service\CurriculumInventory;

use Ilios\CoreBundle\Service\CurriculumInventory\Export\Aggregator;
use Ilios\CoreBundle\Service\CurriculumInventory\Export\XmlPrinter;
use Ilios\CoreBundle\Service\CurriculumInventory\Exporter;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

use Mockery as m;

/**
 * Class ExporterTest
 */
class ExporterTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @covers \Ilios\CoreBundle\Service\CurriculumInventory\Exporter::__construct
     */
    public function testConstructor()
    {
        $aggregator = m::mock(Aggregator::class);
        $printer = m::mock(XmlPrinter::class);
        $exporter = new Exporter($aggregator, $printer);

        $this->assertTrue($exporter instanceof Exporter);
    }
}
