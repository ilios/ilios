<?php
namespace Tests\CoreBundle\Service\CurriculumInventory;

use Ilios\CoreBundle\Entity\Manager\CurriculumInventoryInstitutionManager;
use Ilios\CoreBundle\Service\Config;
use Ilios\CoreBundle\Service\CurriculumInventory\Exporter;
use Ilios\CoreBundle\Entity\Manager\CurriculumInventoryReportManager;
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
        $reportManager = m::mock(CurriculumInventoryReportManager::class);
        $institutionManager = m::mock(CurriculumInventoryInstitutionManager::class);
        $config = m::mock(Config::class);
        $config->shouldReceive('get')->once()->with('institution_domain')->andReturn('');
        $config->shouldReceive('get')->once()->with('supporting_link')->andReturn('');

        $exporter = new Exporter($reportManager, $institutionManager, $config);

        $this->assertTrue($exporter instanceof Exporter);
    }
}
