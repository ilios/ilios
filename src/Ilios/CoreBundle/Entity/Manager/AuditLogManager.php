<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\Repository\AuditLogRepository;

/**
 * Class AuditLogManager
 */
class AuditLogManager extends BaseManager
{
    /**
     * Returns all audit log entries in a given date/time range.
     *
     * @param \DateTime $from
     * @param \DateTime $to
     * @return array
     */
    public function findInRange(\DateTime $from, \DateTime $to)
    {
        return $this->getRepository()->findInRange($from, $to);
    }

    /**
     * Deletes all audit log entries in a given date/time range.
     * @param \DateTime $from
     * @param \DateTime $to
     */
    public function deleteInRange(\DateTime $from, \DateTime $to)
    {
        $this->getRepository()->deleteInRange($from, $to);
    }


    /**
     * Returns a list of field names of the corresponding entity.
     *
     * @return array
     *
     * @todo Refactor this out into a trait or stick it somewhere else. [ST 2015/09/02]
     */
    public function getFieldNames()
    {
        return $this->em->getClassMetadata($this->getClass())->getFieldNames();
    }

    /**
     * Write logs to the databse
     * @param array $entries
     */
    public function writeLogs(array $entries)
    {
        /** @var AuditLogRepository $repository */
        $repository = $this->getRepository();
        $repository->writeLogs($entries);
    }
}
