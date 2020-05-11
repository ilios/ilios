<?php

declare(strict_types=1);

namespace App\Entity\Repository;

use App\Entity\DTO\ProgramYearObjectiveDTO;
use App\Entity\ProgramYearObjective;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;

/**
 * Class ProgramYearObjectiveRepository
 */
class ProgramYearObjectiveRepository extends EntityRepository implements DTORepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from(ProgramYearObjective::class, 'x');

        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        return $qb->getQuery()->getResult();
    }

    /**
     * Find and hydrate as DTOs
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     *
     * @return array
     */
    public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder()->select('x')
            ->distinct()->from(ProgramYearObjective::class, 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
        /** @var ProgramYearObjectiveDTO[] $programYearObjectiveDTOs */
        $programYearObjectiveDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $programYearObjectiveDTOs[$arr['id']] = new ProgramYearObjectiveDTO($arr['id'], $arr['position']);
        }
        $programYearObjectiveIds = array_keys($programYearObjectiveDTOs);

        $qb = $this->_em->createQueryBuilder()
            ->select(
                'x.id AS xId, objective.id AS objectiveId, ' .
                'programYear.id AS programYearId, programYear.locked AS programYearIsLocked, ' .
                'programYear.archived AS programYearIsArchived'
            )
            ->from('App\Entity\ProgramYearObjective', 'x')
            ->join('x.programYear', 'programYear')
            ->join('x.objective', 'objective')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $programYearObjectiveIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $programYearObjectiveDTOs[$arr['xId']]->programYearIsLocked = (bool) $arr['programYearIsLocked'];
            $programYearObjectiveDTOs[$arr['xId']]->programYearIsArchived = (bool) $arr['programYearIsArchived'];
            $programYearObjectiveDTOs[$arr['xId']]->programYear = (int) $arr['programYearId'];
            $programYearObjectiveDTOs[$arr['xId']]->objective = (int) $arr['objectiveId'];
        }

        $related = [
            'terms'
        ];
        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id AS relId, x.id AS programYearObjectiveId')
                ->from('App\Entity\ProgramYearObjective', 'x')
                ->join("x.{$rel}", 'r')
                ->where($qb->expr()->in('x.id', ':ids'))
                ->orderBy('relId')
                ->setParameter('ids', $programYearObjectiveIds);
            foreach ($qb->getQuery()->getResult() as $arr) {
                $programYearObjectiveDTOs[$arr['programYearObjectiveId']]->{$rel}[] = $arr['relId'];
            }
        }
        return array_values($programYearObjectiveDTOs);
    }


    /**
     * @param QueryBuilder $qb
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     *
     * @return QueryBuilder
     */
    protected function attachCriteriaToQueryBuilder(QueryBuilder $qb, $criteria, $orderBy, $limit, $offset)
    {
        if (array_key_exists('terms', $criteria)) {
            if (is_array($criteria['terms'])) {
                $ids = $criteria['terms'];
            } else {
                $ids = [$criteria['terms']];
            }
            $qb->join('x.terms', 'st');
            $qb->andWhere($qb->expr()->in('st.id', ':terms'));
            $qb->setParameter(':terms', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['terms']);

        if (count($criteria)) {
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
}
