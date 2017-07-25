<?php
namespace Tests\CoreBundle\Service\CurriculumInventory;

use Ilios\CoreBundle\Entity\Manager\CurriculumInventoryInstitutionManager;
use Ilios\CoreBundle\Entity\Manager\ManagerInterface;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

use Ilios\CoreBundle\Service\CurriculumInventory\Exporter;
use Ilios\CoreBundle\Entity\Manager\CurriculumInventoryReportManager;

/**
 * Class ExporterTest
 */
class ExporterTest extends TestCase
{

    /**
     * @covers \Ilios\CoreBundle\Service\CurriculumInventory\Exporter::__construct
     */
    public function testConstructor()
    {
        /** @var CurriculumInventoryReportManager $reportManager */
        $reportManager = $this
            ->getMockBuilder(CurriculumInventoryReportManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var ManagerInterface $institutionManager */
        $institutionManager = $this
            ->getMockBuilder(CurriculumInventoryInstitutionManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $exporter = new Exporter($reportManager, $institutionManager, '', '');

        $this->assertTrue($exporter instanceof Exporter);
    }
}
