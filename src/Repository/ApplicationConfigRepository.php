<?php

declare(strict_types=1);

namespace App\Repository;

use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\ApplicationConfig;
use App\Entity\DTO\ApplicationConfigDTO;
use Doctrine\Persistence\ManagerRegistry;

class ApplicationConfigRepository extends ServiceEntityRepository implements
    DTORepositoryInterface,
    RepositoryInterface,
    DataImportRepositoryInterface
{
    use ManagerRepository;

    public function __construct(ManagerRegistry $registry, protected bool $cacheEnabled)
    {
        parent::__construct($registry, ApplicationConfig::class);
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from('App\Entity\ApplicationConfig', 'x');

        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find and hydrate as DTOs
     *
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     *
     */
    public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        $qb = $this->_em->createQueryBuilder()->select('x')->distinct()->from('App\Entity\ApplicationConfig', 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
        $applicationConfigDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $applicationConfigDTOs[] = new ApplicationConfigDTO(
                $arr['id'],
                $arr['name'],
                $arr['value']
            );
        }

        return $applicationConfigDTOs;
    }

    protected function getValues(): array
    {
        static $cache;
        if (! $this->cacheEnabled || ! isset($cache)) {
            $cache = [];

            $qb = $this->_em->createQueryBuilder();
            $qb->select('x.value, x.name')->from(ApplicationConfig::class, 'x');

            $configs = $qb->getQuery()->getArrayResult();

            foreach ($configs as ['name' => $name, 'value' => $value]) {
                $cache[$name] = $value;
            }
        }
        return $cache;
    }

    /**
     * @return mixed|null
     */
    public function getValue(string $name)
    {
        $values = $this->getValues();
        if (array_key_exists($name, $values)) {
            return $values[$name];
        }

        return null;
    }

    /**
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     * @return QueryBuilder
     */
    protected function attachCriteriaToQueryBuilder(QueryBuilder $qb, $criteria, $orderBy, $limit, $offset)
    {
        if ($criteria !== []) {
            foreach ($criteria as $key => $value) {
                $values = is_array($value) ? $value : [$value];
                $qb->andWhere($qb->expr()->in("x.{$key}", ":{$key}"));
                $qb->setParameter(":{$key}", $values);
            }
        }

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('x.' . $sort, $order);
            }
        }

        if ($offset) {
            $qb->setFirstResult($offset);
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb;
    }

    public function import(array $data, string $type = null, string $now = null): void
    {
        // ACHTUNG!
        // we MUST clear the the application config table as part of the import process.
        // Unlike with the aamc_resource_type table, we don't have a way to prevent
        // data from being entered in the application_config table before the data import is run.
        // The migrations that set up the schema will fill this table with some defaults in any case,
        // and if we leave those in place then the insert will fail due to duplicate key constraint violations.
        // So... let's just clear all records out here first. ¯\_(ツ)_/¯
        // [ST 2021/07/22]
        $this->createQueryBuilder('a')->delete()->getQuery()->execute();

        $sql = "INSERT INTO application_config(id, name, value) VALUES (?, ?, ?)";
        $connection = $this->_em->getConnection();
        $connection->executeStatement($sql, $data);
    }
}
