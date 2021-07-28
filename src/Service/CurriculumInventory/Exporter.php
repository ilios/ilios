<?php

declare(strict_types=1);

namespace App\Service\CurriculumInventory;

use App\Entity\CurriculumInventoryReportInterface;
use App\Service\CurriculumInventory\Export\Aggregator;
use App\Service\CurriculumInventory\Export\XmlPrinter;
use Exception;

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
    public function __construct(protected Aggregator $aggregator, protected XmlPrinter $printer)
    {
    }
    /**
     * Loads the curriculum inventory for a given report and exports it as XML document.
     * @param CurriculumInventoryReportInterface $report The report.
     * @return string The fully populated report.
     * @throws Exception
     * @see Aggregator::getData()
     * @see XmlPrinter::print()
     */
    public function getXmlReport(CurriculumInventoryReportInterface $report)
    {
        $inventory = $this->aggregator->getData($report);
        return $this->printer->print($inventory);
    }
}
