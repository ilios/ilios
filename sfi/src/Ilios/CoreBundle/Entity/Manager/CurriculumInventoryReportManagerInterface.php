<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface;

/**
 * Interface CurriculumInventoryReportManagerInterface
 * @package Ilios\CoreBundle\Manager
 */
interface CurriculumInventoryReportManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CurriculumInventoryReportInterface
     */
    public function findCurriculumInventoryReportBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return CurriculumInventoryReportInterface[]|Collection
     */
    public function findCurriculumInventoryReportsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param CurriculumInventoryReportInterface $curriculumInventoryReport
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateCurriculumInventoryReport(
        CurriculumInventoryReportInterface $curriculumInventoryReport,
        $andFlush = true
    );

    /**
     * @param CurriculumInventoryReportInterface $curriculumInventoryReport
     *
     * @return void
     */
    public function deleteCurriculumInventoryReport(
        CurriculumInventoryReportInterface $curriculumInventoryReport
    );

    /**
     * @return string
     */
    public function getClass();

    /**
     * @return CurriculumInventoryReportInterface
     */
    public function createCurriculumInventoryReport();
}
