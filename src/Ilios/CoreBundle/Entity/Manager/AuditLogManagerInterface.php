<?php

namespace Ilios\CoreBundle\Entity\Manager;

use Ilios\CoreBundle\Entity\AuditLogInterface;

/**
 * Interface AuditLogManagerInterface
 * @package Ilios\CoreBundle\Entity\Manager
 */
interface AuditLogManagerInterface
{
    /**
     * @param array $criteria
     * @param array $orderBy
     *
     * @return AuditLogInterface
     */
    public function findAuditLogBy(
        array $criteria,
        array $orderBy = null
    );

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param integer $limit
     * @param integer $offset
     *
     * @return AuditLogInterface[]
     */
    public function findAuditLogsBy(
        array $criteria,
        array $orderBy = null,
        $limit = null,
        $offset = null
    );

    /**
     * @param AuditLogInterface $auditLog
     * @param bool $andFlush
     * @param bool $forceId
     *
     * @return void
     */
    public function updateAuditLog(
        AuditLogInterface $auditLog,
        $andFlush = true,
        $forceId = false
    );

    /**
     * @param AuditLogInterface $auditLog
     *
     * @return void
     */
    public function deleteAuditLog(
        AuditLogInterface $auditLog
    );

    /**
     * @return AuditLogInterface
     */
    public function createAuditLog();

    /**
     * Returns all audit log entries in a given date/time range.
     *
     * @param \DateTime $from
     * @param \DateTime $to
     * @return array
     */
    public function findInRange(\DateTime $from, \DateTime $to);

    /**
     * Deletes all audit log entries in a given date/time range.
     * @param \DateTime $from
     * @param \DateTime $to
     */
    public function deleteInRange(\Datetime $from, \DateTime $to);

    /**
     * Returns a list of field names of the corresponding entity.
     *
     * @return array
     *
     * @todo Refactor this out into a trait or stick it somewhere else. [ST 2015/09/02]
     */
    public function getFieldNames();
}
