<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\ORM\Id\AssignedGenerator;
use Doctrine\ORM\QueryBuilder;

/**
 * Trait ManagerRepository
 * Applied to Entity repositories to give them generic repository functionality
 * Relies on the repository extending Doctrine\ORM\EntityRepository
 * and implementing App\Repository\DTORepositoryInterface
 */
trait ManagerRepository
{
    abstract protected function getEntityName();
    abstract protected function getEntityManager();
    abstract public function find($id);
    abstract public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array;

    public function getClass(): string
    {
        return $this->getEntityName();
    }

    public function flushAndClear(): void
    {
        $this->getEntityManager()->flush();
        $this->getEntityManager()->clear();
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }

    public function findOneById($id): ?object
    {
        return $this->find($id);
    }

    public function findDTOBy(array $criteria): ?object
    {
        $results = $this->findDTOsBy($criteria, null, 1);
        return $results[0] ?? null;
    }

    public function update($entity, $andFlush = true, $forceId = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($forceId) {
            $metadata = $this->getEntityManager()->getClassMetaData($entity::class);
            $metadata->setIdGenerator(new AssignedGenerator());
        }

        if ($andFlush) {
            $this->getEntityManager()->flush();
        }
    }

    public function delete($entity): void
    {
        $this->getEntityManager()->remove($entity);
        $this->getEntityManager()->flush();
    }

    public function create(): object
    {
        $class = $this->getEntityName();
        return new $class();
    }

    public function getIdField(): string
    {
        $meta = $this->getEntityManager()->getClassMetadata($this->getEntityName());
        return $meta->getSingleIdentifierFieldName();
    }

    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('DISTINCT x')->from($this->getEntityName(), 'x');

        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        return $qb->getQuery()->getResult();
    }

    protected function attachRelatedToDtos(
        array $dtos,
        array $related,
    ): array {
        $ids = array_keys($dtos);
        foreach ($related as $rel) {
            $qb = $this->getEntityManager()->createQueryBuilder();
            $qb->select('r.id AS relId, x.id AS xId')->from($this->getEntityName(), 'x')
                ->join("x.{$rel}", 'r')
                ->where($qb->expr()->in('x.id', ':ids'))
                ->orderBy('relId')
                ->setParameter('ids', $ids);
            foreach ($qb->getQuery()->getResult() as $arr) {
                $dtos[$arr['xId']]->{$rel}[] = $arr['relId'];
            }
        }

        return $dtos;
    }

    protected function attachClosingCriteriaToQueryBuilder(
        QueryBuilder $qb,
        array $criteria,
        ?array $orderBy,
        ?int $limit,
        ?int $offset
    ): void {
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
    }

    abstract protected function attachCriteriaToQueryBuilder(
        QueryBuilder $qb,
        array $criteria,
        ?array $orderBy,
        ?int $limit,
        ?int $offset
    ): void;
}
