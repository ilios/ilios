<?php

declare(strict_types=1);

namespace App\Traits;

use Doctrine\ORM\QueryBuilder;

use function array_keys;
use function is_array;

trait FindByRepository
{
    abstract protected function getEntityName();
    abstract protected function getEntityManager();

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

    abstract protected function getClass(): string;

    abstract protected function attachCriteriaToQueryBuilder(
        QueryBuilder $qb,
        array $criteria,
        ?array $orderBy,
        ?int $limit,
        ?int $offset
    ): void;
}
