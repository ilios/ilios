<?php

namespace App\Entity;

use App\Traits\BlameableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
use App\Traits\TimestampableEntityInterface;

use App\Entity\CurriculumInventoryReportInterface;

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
