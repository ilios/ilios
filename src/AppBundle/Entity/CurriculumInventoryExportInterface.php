<?php

namespace AppBundle\Entity;

use AppBundle\Traits\BlameableEntityInterface;
use AppBundle\Traits\IdentifiableEntityInterface;
use AppBundle\Traits\TimestampableEntityInterface;

use AppBundle\Entity\CurriculumInventoryReportInterface;

/**
 * Interface CurriculumInventoryExportInterface
 */
interface CurriculumInventoryExportInterface extends
    IdentifiableEntityInterface,
    LoggableEntityInterface
{
    /**
     * @param string $document
     */
    public function setDocument($document);

    /**
     * @return string
     */
    public function getDocument();

    /**
     * @param CurriculumInventoryReportInterface $report
     */
    public function setReport(CurriculumInventoryReportInterface $report);

    /**
     * @return CurriculumInventoryReportInterface
     */
    public function getReport();

    /**
     * @param UserInterface $createdBy
     */
    public function setCreatedBy(UserInterface $createdBy);

    /**
     * @return UserInterface
     */
    public function getCreatedBy();

    /**
     * @return \DateTime
     */
    public function getCreatedAt();
}
