<?php

declare(strict_types=1);

namespace App\Entity\Repository;

use App\Entity\DTO\SessionObjectiveDTO;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;

/**
 * Class SessionObjectiveRepository
 */
class SessionObjectiveRepository extends EntityRepository implements DTORepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from('App\Entity\SessionObjective', 'x');

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
            ->distinct()->from('App\Entity\SessionObjective', 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
        /** @var SessionObjectiveDTO[] $sessionObjectiveDTOs */
        $sessionObjectiveDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $sessionObjectiveDTOs[$arr['id']] = new SessionObjectiveDTO($arr['id'], $arr['position']);
        }
        $sessionObjectiveIds = array_keys($sessionObjectiveDTOs);

        $qb = $this->_em->createQueryBuilder()
            ->select(
                'x.id AS xId, objective.id AS objectiveId, session.id AS sessionId, ' .
                'course.id AS courseId, course.locked AS courseIsLocked, course.archived AS courseIsArchived, ' .
                'school.id AS schoolId'
            )
            ->from('App\Entity\SessionObjective', 'x')
            ->join('x.session', 'session')
            ->join('session.course', 'course')
            ->join('course.school', 'school')
            ->join('x.objective', 'objective')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $sessionObjectiveIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $sessionObjectiveDTOs[$arr['xId']]->session = (int) $arr['sessionId'];
            $sessionObjectiveDTOs[$arr['xId']]->courseIsLocked = (bool) $arr['courseIsLocked'];
            $sessionObjectiveDTOs[$arr['xId']]->courseIsArchived = (bool) $arr['courseIsArchived'];
            $sessionObjectiveDTOs[$arr['xId']]->course = (int) $arr['courseId'];
            $sessionObjectiveDTOs[$arr['xId']]->school = (int) $arr['schoolId'];
            $sessionObjectiveDTOs[$arr['xId']]->objective = (int) $arr['objectiveId'];
        }

        $related = [
            'terms'
        ];
        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id AS relId, x.id AS sessionObjectiveId')
                ->from('App\Entity\SessionObjective', 'x')
                ->join("x.{$rel}", 'r')
                ->where($qb->expr()->in('x.id', ':ids'))
                ->orderBy('relId')
                ->setParameter('ids', $sessionObjectiveIds);
            foreach ($qb->getQuery()->getResult() as $arr) {
                $sessionObjectiveDTOs[$arr['sessionObjectiveId']]->{$rel}[] = $arr['relId'];
            }
        }
        return array_values($sessionObjectiveDTOs);
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
