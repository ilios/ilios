<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\AuditLog;
use App\Service\DTOCacheTagger;
use App\Traits\ManagerRepository;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Flagception\Manager\FeatureManagerInterface;
use Symfony\Contracts\Cache\CacheInterface;

class AuditLogRepository extends ServiceEntityRepository implements DTORepositoryInterface, RepositoryInterface
{
    use ManagerRepository;

    public function __construct(
        ManagerRegistry $registry,
        protected CacheInterface $cache,
        protected DTOCacheTagger $cacheTagger,
        protected FeatureManagerInterface $featureManager,
    ) {
        parent::__construct($registry, AuditLog::class);
    }

    public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        throw new Exception('DTOs for AuditLogs are not implemented yet');
    }

    /**
     * Returns all audit log entries in a given date/time range.
     */
    public function findInRange(DateTime $from, DateTime $to): array
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('a as log', 'u.id as userId')
            ->from('App\Entity\AuditLog', 'a')
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
     */
    public function deleteInRange(DateTime $from, DateTime $to)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb
            ->delete('App\Entity\AuditLog', 'a')
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
     *
     * @throws Exception where there are issues with the passed data
     */
    public function writeLogs(array $entries)
    {
        $conn = $this->_em->getConnection();
        $now = new DateTime();
        $timestamp = $now->format('Y-m-d H:i:s');
        $logs = array_map(function (array $entry) use ($timestamp) {
            $keys = ['action', 'objectId', 'objectClass', 'valuesChanged', 'userId'];
            $log = [];
            foreach ($keys as $key) {
                if (!array_key_exists($key, $entry)) {
                    throw new Exception("Log entry missing required {$key} key: " . var_export($entry, true));
                }
            }
            $log['action'] = $entry['action'];
            $log['objectId'] = empty($entry['objectId']) ? 0 : $entry['objectId'];
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

    protected function attachCriteriaToQueryBuilder(
        QueryBuilder $qb,
        array $criteria,
        ?array $orderBy,
        ?int $limit,
        ?int $offset
    ): void {
        //empty as DTOs aren't implemented here
    }

    protected function hydrateDTOsFromIds(array $ids): array
    {
        throw new Exception('DTOs for AuditLogs are not implemented yet');
    }
}
