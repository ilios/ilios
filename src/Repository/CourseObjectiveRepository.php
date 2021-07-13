<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\CourseObjective;
use App\Entity\DTO\CourseObjectiveDTO;
use App\Traits\ManagerRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;

class CourseObjectiveRepository extends ServiceEntityRepository implements DTORepositoryInterface, RepositoryInterface
{
    use ManagerRepository;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CourseObjective::class);
    }

    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from(CourseObjective::class, 'x');

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
        $qb = $this->_em->createQueryBuilder()->select('x')
            ->distinct()->from(CourseObjective::class, 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
        /** @var CourseObjectiveDTO[] $courseObjectiveDTOs */
        $courseObjectiveDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $courseObjectiveDTOs[$arr['id']] = new CourseObjectiveDTO(
                $arr['id'],
                $arr['title'],
                $arr['position'],
                $arr['active']
            );
        }
        $courseObjectiveIds = array_keys($courseObjectiveDTOs);

        $qb = $this->_em->createQueryBuilder()
            ->select(
                'x.id AS xId, ' .
                'course.id AS courseId, course.locked AS courseIsLocked, course.archived AS courseIsArchived, ' .
                'school.id AS schoolId'
            )
            ->from(CourseObjective::class, 'x')
            ->join('x.course', 'course')
            ->join('course.school', 'school')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $courseObjectiveIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $courseObjectiveDTOs[$arr['xId']]->courseIsLocked = (bool) $arr['courseIsLocked'];
            $courseObjectiveDTOs[$arr['xId']]->courseIsArchived = (bool) $arr['courseIsArchived'];
            $courseObjectiveDTOs[$arr['xId']]->course = (int) $arr['courseId'];
            $courseObjectiveDTOs[$arr['xId']]->school = (int) $arr['schoolId'];
        }

        $qb = $this->_em->createQueryBuilder()
            ->select('x.id as id, a.id as ancestorId')
            ->from(CourseObjective::class, 'x')
            ->leftJoin('x.ancestor', 'a')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $courseObjectiveIds);
        foreach ($qb->getQuery()->getResult() as $arr) {
            $courseObjectiveDTOs[$arr['id']]->ancestor = $arr['ancestorId'] ? (int)$arr['ancestorId'] : null;
        }

        $related = [
            'terms',
            'programYearObjectives',
            'sessionObjectives',
            'meshDescriptors',
            'descendants'
        ];
        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id AS relId, x.id AS courseObjectiveId')
                ->from(CourseObjective::class, 'x')
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
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
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
        if (array_key_exists('courses', $criteria)) {
            $ids = is_array($criteria['courses']) ? $criteria['courses'] : [$criteria['courses']];
            $qb->join('x.course', 'c');
            $qb->andWhere($qb->expr()->in('c.id', ':courses'));
            $qb->setParameter(':courses', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['terms']);
        unset($criteria['courses']);

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

    /**
     * @throws NoResultException
     * @throws NonUniqueResultException
     */
    public function getTotalObjectiveCount(): int
    {
        return (int) $this->_em->createQuery('SELECT COUNT(o.id) FROM App\Entity\CourseObjective o')
            ->getSingleScalarResult();
    }
}
