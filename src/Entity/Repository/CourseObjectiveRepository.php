<?php

declare(strict_types=1);

namespace App\Entity\Repository;

use App\Entity\DTO\CourseObjectiveDTO;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;

/**
 * Class CourseObjectiveRepository
 */
class CourseObjectiveRepository extends EntityRepository implements DTORepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from('App\Entity\CourseObjective', 'x');

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
            ->distinct()->from('App\Entity\CourseObjective', 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
        /** @var CourseObjectiveDTO[] $courseObjectiveDTOs */
        $courseObjectiveDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $courseObjectiveDTOs[$arr['id']] = new CourseObjectiveDTO($arr['id'], $arr['position']);
        }
        $courseObjectiveIds = array_keys($courseObjectiveDTOs);

        $qb = $this->_em->createQueryBuilder()
            ->select(
                'x.id AS xId, objective.id AS objectiveId, ' .
                'course.id AS courseId, course.locked AS courseIsLocked, course.archived AS courseIsArchived, ' .
                'school.id AS schoolId'
            )
            ->from('App\Entity\CourseObjective', 'x')
            ->join('x.course', 'course')
            ->join('course.school', 'school')
            ->join('x.objective', 'objective')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $courseObjectiveIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $courseObjectiveDTOs[$arr['xId']]->courseIsLocked = (bool) $arr['courseIsLocked'];
            $courseObjectiveDTOs[$arr['xId']]->courseIsArchived = (bool) $arr['courseIsArchived'];
            $courseObjectiveDTOs[$arr['xId']]->course = (int) $arr['courseId'];
            $courseObjectiveDTOs[$arr['xId']]->school = (int) $arr['schoolId'];
            $courseObjectiveDTOs[$arr['xId']]->objective = (int) $arr['objectiveId'];
        }

        $related = [
            'terms'
        ];
        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id AS relId, x.id AS courseObjectiveId')
                ->from('App\Entity\CourseObjective', 'x')
                ->join("x.{$rel}", 'r')
                ->where($qb->expr()->in('x.id', ':ids'))
                ->orderBy('relId')
                ->setParameter('ids', $courseObjectiveIds);
            foreach ($qb->getQuery()->getResult() as $arr) {
                $courseObjectiveDTOs[$arr['courseObjectiveId']]->{$rel}[] = $arr['relId'];
            }
        }
        return array_values($courseObjectiveDTOs);
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
