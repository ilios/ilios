<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\BlameableEntityInterface;
use App\Traits\IdentifiableEntityInterface;
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

    public function setReport(CurriculumInventoryReportInterface $report);

    /**
     * @return CurriculumInventoryReportInterface
     */
    public function getReport();

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
