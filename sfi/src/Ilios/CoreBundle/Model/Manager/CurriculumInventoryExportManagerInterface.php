<?php

namespace Ilios\CoreBundle\Model\Manager;

use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Model\CurriculumInventoryExportInterface;

/**
 * Interface CurriculumInventoryExportManagerInterface
 */
interface CurriculumInventoryExportManagerInterface
{
    /** 
     *@return CurriculumInventoryExportInterface
     */
    public function createCurriculumInventoryExport();

    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return CurriculumInventoryExportInterface
     */
    public function findCurriculumInventoryExportBy(array $criteria, array $orderBy = null);

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return CurriculumInventoryExportInterface[]|Collection
     */
    public function findCurriculumInventoryExportsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null);

    /**
     * @param CurriculumInventoryExportInterface $curriculumInventoryExport
     * @param bool $andFlush
     *
     * @return void
     */
    public function updateCurriculumInventoryExport(CurriculumInventoryExportInterface $curriculumInventoryExport, $andFlush = true);

    /**
     * @param CurriculumInventoryExportInterface $curriculumInventoryExport
     *
     * @return void
     */
    public function deleteCurriculumInventoryExport(CurriculumInventoryExportInterface $curriculumInventoryExport);

    /**
     * @return string
     */
    public function getClass();
}
