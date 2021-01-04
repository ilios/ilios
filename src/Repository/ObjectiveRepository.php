<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\DTO\ObjectiveV1DTO;
use App\Entity\Manager\ManagerInterface;
use App\Entity\Objective;
use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Persistence\ManagerRegistry;

class ObjectiveRepository extends ServiceEntityRepository implements DTORepositoryInterface, ManagerInterface
{
    use ManagerRepository;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Objective::class);
    }

    /**
     * Custom findBy so we can filter by related entities
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     *
     * @return array
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT o')->from('App\Entity\Objective', 'o');

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
    public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        return $this->findV1DTOsBy($criteria, $orderBy, $limit, $offset);
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
    public function findV1DTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder()->select('o')->distinct()->from(Objective::class, 'o');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
        $objectiveDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $objectiveDTOs[$arr['id']] = new ObjectiveV1DTO(
                $arr['id'],
                $arr['title'],
                $arr['active']
            );
        }
        $objectiveIds = array_keys($objectiveDTOs);
        $qb = $this->_em->createQueryBuilder()
            ->select('o.id as objectiveId, c.id as competencyId, a.id as ancestorId')
            ->from('App\Entity\Objective', 'o')
            ->leftJoin('o.competency', 'c')
            ->leftJoin('o.ancestor', 'a')
            ->where($qb->expr()->in('o.id', ':ids'))
            ->setParameter('ids', $objectiveIds);
        foreach ($qb->getQuery()->getResult() as $arr) {
            $objectiveDTOs[$arr['objectiveId']]->competency = $arr['competencyId'] ? (int)$arr['competencyId'] : null;
            $objectiveDTOs[$arr['objectiveId']]->ancestor = $arr['ancestorId'] ? (int)$arr['ancestorId'] : null;
        }
        $related = [
            'parents',
            'children',
            'meshDescriptors',
            'descendants'
        ];
        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id AS relId, o.id AS objectiveId')->from('App\Entity\Objective', 'o')
                ->join("o.{$rel}", 'r')
                ->where($qb->expr()->in('o.id', ':objectiveIds'))
                ->orderBy('relId')
                ->setParameter('objectiveIds', $objectiveIds);
            foreach ($qb->getQuery()->getResult() as $arr) {
                $objectiveDTOs[$arr['objectiveId']]->{$rel}[] = $arr['relId'];
            }
        }
        $related = [
            'course' => 'courses',
            'session' => 'sessions',
            'programYear' => 'programYears',
        ];

        foreach ($related as $rel => $attr) {
            $qb = $this->_em->createQueryBuilder()
                ->select("ro.position AS position, r.id AS relId, o.id AS objectiveId")
                ->from(Objective::class, 'o')
                ->join("o.{$rel}Objectives", 'ro')
                ->join("ro.${rel}", 'r')
                ->where($qb->expr()->in('o.id', ':objectiveIds'))
                ->orderBy('relId')
                ->setParameter('objectiveIds', $objectiveIds);
            $positionHasBeenApplied = false;
            foreach ($qb->getQuery()->getResult() as $arr) {
                $objectiveDTOs[$arr['objectiveId']]->{$attr}[] = $arr['relId'];
                if (! $positionHasBeenApplied) {
                    $objectiveDTOs[$arr['objectiveId']]->position = $arr['position'];
                    $positionHasBeenApplied = true;
                }
            }
        }

        return array_values($objectiveDTOs);
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
        if (array_key_exists('courses', $criteria)) {
            $ids = is_array($criteria['courses']) ? $criteria['courses'] : [$criteria['courses']];
            $qb->join('o.courseObjectives', 'courseObjective');
            $qb->join('courseObjective.course', 'course');
            $qb->andWhere($qb->expr()->in('course.id', ':courses'));
            $qb->setParameter(':courses', $ids);
        }
        if (array_key_exists('programYears', $criteria)) {
            $ids = is_array($criteria['programYears']) ? $criteria['programYears'] : [$criteria['programYears']];
            $qb->join('o.programYearObjectives', 'programYearObjective');
            $qb->join('programYearObjective.programYear', 'programYear');
            $qb->andWhere($qb->expr()->in('programYear.id', ':programYears'));
            $qb->setParameter(':programYears', $ids);
        }
        if (array_key_exists('sessions', $criteria)) {
            $ids = is_array($criteria['sessions']) ? $criteria['sessions'] : [$criteria['sessions']];
            $qb->join('o.sessionObjectives', 'sessionObjective');
            $qb->join('sessionObjective.session', 'session');
            $qb->andWhere($qb->expr()->in('session.id', ':sessions'));
            $qb->setParameter(':sessions', $ids);
        }
        if (array_key_exists('courseObjectives', $criteria)) {
            $ids = is_array($criteria['courseObjectives'])
                ? $criteria['courseObjectives'] : [$criteria['courseObjectives']];
            $qb->join('o.courseObjectives', 'courseObjective2');
            $qb->andWhere($qb->expr()->in('courseObjective2.id', ':courseObjectives'));
            $qb->setParameter(':courseObjectives', $ids);
        }
        if (array_key_exists('programYearObjectives', $criteria)) {
            $ids = is_array($criteria['programYearObjectives'])
                ? $criteria['programYearObjectives'] : [$criteria['programYearObjectives']];
            $qb->join('o.programYearObjectives', 'programYearObjective2');
            $qb->andWhere($qb->expr()->in('programYearObjective2.id', ':programYearObjectives'));
            $qb->setParameter(':programYearObjectives', $ids);
        }
        if (array_key_exists('sessionObjectives', $criteria)) {
            $ids = is_array($criteria['sessionObjectives'])
                ? $criteria['sessionObjectives'] : [$criteria['sessionObjectives']];
            $qb->join('o.sessionObjectives', 'sessionObjective2');
            $qb->andWhere($qb->expr()->in('sessionObjective2.id', ':sessionObjectives'));
            $qb->setParameter(':sessionObjectives', $ids);
        }
        if (array_key_exists('fullCourses', $criteria)) {
            $ids = is_array($criteria['fullCourses']) ? $criteria['fullCourses'] : [$criteria['fullCourses']];
            $qb->leftJoin('o.courseObjectives', 'f_course_objective');
            $qb->leftJoin('f_course_objective.course', 'f_course');
            $qb->leftJoin('o.sessionObjectives', 'f_session_objective');
            $qb->leftJoin('f_session_objective.session', 'f_sessions');
            $qb->leftJoin('f_sessions.course', 'f_session_course');
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->in('f_course.id', ':fullCourses'),
                $qb->expr()->in('f_session_course.id', ':fullCourses')
            ));
            $qb->setParameter(':fullCourses', $ids);
        }

        unset($criteria['courses']);
        unset($criteria['programYears']);
        unset($criteria['sessions']);
        unset($criteria['courseObjectives']);
        unset($criteria['programYearObjectives']);
        unset($criteria['sessionObjectives']);
        unset($criteria['fullCourses']);

        if (count($criteria)) {
            foreach ($criteria as $key => $value) {
                $values = is_array($value) ? $value : [$value];
                $qb->andWhere($qb->expr()->in("o.{$key}", ":{$key}"));
                $qb->setParameter(":{$key}", $values);
            }
        }

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('o.' . $sort, $order);
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
