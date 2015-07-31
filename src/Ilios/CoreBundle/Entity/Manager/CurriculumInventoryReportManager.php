<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface;

/**
 * Class CurriculumInventoryReportManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class CurriculumInventoryReportManager extends AbstractManager implements CurriculumInventoryReportManagerInterface
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
    ) {
        return $this->repository->findOneBy($criteria, $orderBy);
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return ArrayCollection|CurriculumInventoryReportInterface[]
     */
    public function findCurriculumInventoryReportsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->repository->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * @param CurriculumInventoryReportInterface $curriculumInventoryReport
     * @param bool $andFlush
     * @param bool $forceId
     */
    public function updateCurriculumInventoryReport(
        CurriculumInventoryReportInterface $curriculumInventoryReport,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($curriculumInventoryReport);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($curriculumInventoryReport));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * @param CurriculumInventoryReportInterface $curriculumInventoryReport
     */
    public function deleteCurriculumInventoryReport(
        CurriculumInventoryReportInterface $curriculumInventoryReport
    ) {
        $this->em->remove($curriculumInventoryReport);
        $this->em->flush();
    }

    /**
     * @return CurriculumInventoryReportInterface
     */
    public function createCurriculumInventoryReport()
    {
        $class = $this->getClass();
        return new $class();
    }
}
