<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\CurriculumInventoryExportInterface;

/**
 * Class CurriculumInventoryExportManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CurriculumInventoryExportManager extends BaseManager
{
    /**
     * @deprecated
     */
    public function findCurriculumInventoryExportBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->findOneBy($criteria, $orderBy);
    }

    /**
     * @deprecated
     */
    public function findCurriculumInventoryExportsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @deprecated
     */
    public function updateCurriculumInventoryExport(
        CurriculumInventoryExportInterface $curriculumInventoryExport,
        $andFlush = true,
        $forceId = false
    ) {
        $this->update($curriculumInventoryExport, $andFlush, $forceId);
    }

    /**
     * @deprecated
     */
    public function deleteCurriculumInventoryExport(
        CurriculumInventoryExportInterface $curriculumInventoryExport
    ) {
        $this->delete($curriculumInventoryExport);
    }

    /**
     * @deprecated
     */
    public function createCurriculumInventoryExport()
    {
        return $this->create();
    }
}
