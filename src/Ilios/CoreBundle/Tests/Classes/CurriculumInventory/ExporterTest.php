<?php
namespace Ilios\CoreBundle\Tests\Classes\CurriculumInventory;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;

use Ilios\CoreBundle\Classes\CurriculumInventory\Exporter;
use Ilios\CoreBundle\Entity\Manager\CurriculumInventoryInstitutionManager;
use Ilios\CoreBundle\Entity\Manager\CurriculumInventoryReportManager;


/**
 * Class ExporterTest
 * @package Ilios\CoreBundle\Tests\Classes\CurriculumInventory
 */
class ExporterTest extends TestCase
{

    /**
     * @covers Ilios\CoreBundle\Classes\CurriculumInventory\Exporter::__construct
     */
    public function testConstructor()
    {
        /** @var CurriculumInventoryReportManager $reportManager */
        $reportManager = $this
            ->getMockBuilder('Ilios\CoreBundle\Entity\Manager\CurriculumInventoryReportManager')
            ->disableOriginalConstructor()
            ->getMock();

        /** @var CurriculumInventoryInstitutionManager $institutionManager */
        $institutionManager = $this
            ->getMockBuilder('Ilios\CoreBundle\Entity\Manager\CurriculumInventoryInstitutionManager')
            ->disableOriginalConstructor()
            ->getMock();

        $exporter = new Exporter($reportManager, $institutionManager, '', '');

        $this->assertTrue($exporter instanceof Exporter);
    }
}
