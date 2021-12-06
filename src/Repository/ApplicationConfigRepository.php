<?php

declare(strict_types=1);

namespace App\Repository;

use App\Traits\ImportableEntityRepository;
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
    use ImportableEntityRepository;

    public function __construct(ManagerRegistry $registry, protected bool $cacheEnabled)
    {
        parent::__construct($registry, ApplicationConfig::class);
    }

    /**
     * Find and hydrate as DTOs
     */
    public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        $qb = $this->_em->createQueryBuilder()->select('x')->distinct()->from(ApplicationConfig::class, 'x');
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

    public function getValue(string $name): mixed
    {
        $values = $this->getValues();
        if (array_key_exists($name, $values)) {
            return $values[$name];
        }

        return null;
    }

    protected function attachCriteriaToQueryBuilder(
        QueryBuilder $qb,
        array $criteria,
        ?array $orderBy,
        ?int $limit,
        ?int $offset
    ): void {
        $this->attachClosingCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
    }

    public function import(array $data, string $type, array $referenceMap): array
    {
        // `id`, `name`,`value`
        $entity = new ApplicationConfig();
        $entity->setId($data[0]);
        $entity->setName($data[1]);
        $entity->setValue($data[2]);
        $this->importEntity($entity);
        $referenceMap[$type . $entity->getId()] = $entity;
        return $referenceMap;
    }

    /**
     * Delete all records in this table
     */
    public function deleteAll(): void
    {
        $this->createQueryBuilder('a')->delete()->getQuery()->execute();
    }
}
