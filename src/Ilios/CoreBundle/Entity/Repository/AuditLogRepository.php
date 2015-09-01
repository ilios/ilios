<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class AuditLogRepository
 * @package Ilios\CoreBundle\Entity\Repository
 */
class AuditLogRepository extends EntityRepository
{
    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public function getAuditEventsInRange(\DateTime $from, \DateTime $to, $limit = 0, $offset = 0)
    {
        // TODO implement. [ST 2015/09/01]
        return [];
    }

    /**
     * @param \DateTime $from
     * @param \DateTime $to
     * @return int
     */
    public function getNumberOfAuditEventsInRange(\DateTime $from, \DateTime $to)
    {
        // TODO implement. [ST 2015/09/01]
        return 0;
    }
}
