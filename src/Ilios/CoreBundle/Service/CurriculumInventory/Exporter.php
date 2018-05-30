<?php

namespace Ilios\CoreBundle\Service\CurriculumInventory;

use Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface;
use Ilios\CoreBundle\Service\CurriculumInventory\Export\Aggregator;
use Ilios\CoreBundle\Service\CurriculumInventory\Export\XmlPrinter;

/**
 * Curriculum Inventory Exporter.
 *
 * Provides functionality for generating curriculum inventory reports and for exporting reports to XML according
 * to the MedBiquitous specification.
 *
 * @link http://www.medbiq.org/sites/default/files/files/CurriculumInventorySpecification.pdf
 * @link http://ns.medbiq.org/curriculuminventory/v1/curriculuminventory.xsd
 *
 */
class Exporter
{
    /**
     * @var Aggregator
     */
    protected $aggregator;

    /**
     * @var XmlPrinter
     */
    protected $printer;

    /**
     * @param Aggregator $aggregator
     * @param XmlPrinter $printer
     */
    public function __construct(Aggregator $aggregator, XmlPrinter $printer)
    {
        $this->aggregator = $aggregator;
        $this->printer = $printer;
    }
    /**
     * Loads the curriculum inventory for a given report and exports it as XML document.
     * @param CurriculumInventoryReportInterface $report The report.
     * @return \DomDocument The fully populated report.
     * @throws \Exception
     * @see Aggregator::getData()
     * @see XmlPrinter::print()
     */
    public function getXmlReport(CurriculumInventoryReportInterface $report)
    {
        $inventory = $this->aggregator->getData($report);
        return $this->printer->print($inventory);
    }
}
