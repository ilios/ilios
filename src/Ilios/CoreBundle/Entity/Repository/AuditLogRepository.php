<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class AuditLogRepository
 */
class AuditLogRepository extends EntityRepository implements DTORepositoryInterface
{
    public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        throw new \Exception('DTOs for AuditLogs are not implemented yet');
    }

    /**
     * Returns all audit log entries in a given date/time range.
     *
     * @param \DateTime $from
     * @param \DateTime $to
     * @return array
     */
    public function findInRange(\DateTime $from, \DateTime $to)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('a as log', 'u.id as userId')
            ->from('IliosCoreBundle:AuditLog', 'a')
            ->leftJoin('a.user', 'u')
            ->where(
                $qb->expr()->between(
                    'a.createdAt',
                    ':from',
                    ':to'
                )
            )
            ->setParameters(
                [
                    'from' => $from->format('Y-m-d H:i:s'),
                    'to' => $to->format('Y-m-d H:i:s:'),
                ]
            );

        $results = $qb->getQuery()->getArrayResult();
        $rhett = [];
        foreach ($results as $arr) {
            $combined = $arr['log'];
            $combined['userId'] = $arr['userId'];

            $rhett[] = $combined;
        }

        return $rhett;
    }

    /**
     * Deletes all audit log entries in a given date/time range.
     *
     * @param \DateTime $from
     * @param \DateTime $to
     */
    public function deleteInRange(\DateTime $from, \DateTime $to)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb
            ->delete('IliosCoreBundle:AuditLog', 'a')
            ->add(
                'where',
                $qb->expr()->between(
                    'a.createdAt',
                    ':from',
                    ':to'
                )
            )
            ->setParameters(
                [
                    'from' => $from->format('Y-m-d H:i:s'),
                    'to' => $to->format('Y-m-d H:i:s:'),
                ]
            );
        $qb->getQuery()->execute();
    }

    /**
     * Write logs to the database
     *
     * We use the DBAL layer here so we can insert with the userId and
     * do not need to access the user entity
     *
     * @param array $entries
     *
     * @throws \Exception where there are issues with the passed data
     */
    public function writeLogs(array $entries)
    {
        $conn = $this->_em->getConnection();
        $now = new \DateTime();
        $timestamp = $now->format('Y-m-d H:i:s');
        $logs = array_map(function (array $entry) use ($timestamp) {
            $keys = ['action', 'objectId', 'objectClass', 'valuesChanged', 'userId'];
            $log = [];
            foreach ($keys as $key) {
                if (!array_key_exists($key, $entry)) {
                    throw new \Exception("Log entry missing required {$key} key: " . var_export($entry, true));
                }
            }
            $log['action'] = $entry['action'];
            $log['objectId'] = empty($entry['objectId'])?0:$entry['objectId'];
            $log['objectClass'] = $entry['objectClass'];
            $log['valuesChanged'] = $entry['valuesChanged'];
            $log['user_id'] = $entry['userId'];
            $log['createdAt'] = $timestamp;

            return $log;
        }, $entries);

        foreach ($logs as $log) {
            $conn->insert('audit_log', $log);
        }
    }
}
