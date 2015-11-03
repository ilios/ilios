<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Doctrine\ORM\Id\AssignedGenerator;
use Ilios\CoreBundle\Entity\ReportInterface;

/**
 * Class ReportManager
 * @package Ilios\CoreBundle\Entity\Manager
 */
class ReportManager extends AbstractManager implements ReportManagerInterface
{
    /**
     * {@inheritdoc}
     */
    public function findReportBy(
        array $criteria,
        array $orderBy = null
    ) {
        return $this->getRepository()->findOneBy($criteria, $orderBy);
    }

    /**
     * {@inheritdoc}
     */
    public function findReportsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    ) {
        return $this->getRepository()->findBy($criteria, $orderBy, $limit, $offset);
    }

    /**
     * {@inheritdoc}
     */
    public function updateReport(
        ReportInterface $report,
        $andFlush = true,
        $forceId = false
    ) {
        $this->em->persist($report);

        if ($forceId) {
            $metadata = $this->em->getClassMetaData(get_class($report));
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->em->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function deleteReport(
        ReportInterface $report
    ) {
        $this->em->remove($report);
        $this->em->flush();
    }

    /**
     * {@inheritdoc}
     */
    public function createReport()
    {
        $class = $this->getClass();
        return new $class();
    }
}
