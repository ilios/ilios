<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\SessionObjective;
use App\Entity\DTO\SessionObjectiveDTO;
use App\Traits\ManagerRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;

class SessionObjectiveRepository extends ServiceEntityRepository implements DTORepositoryInterface, RepositoryInterface
{
    use ManagerRepository;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SessionObjective::class);
    }

    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from(SessionObjective::class, 'x');

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
            ->distinct()->from(SessionObjective::class, 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
        /** @var SessionObjectiveDTO[] $sessionObjectiveDTOs */
        $sessionObjectiveDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $sessionObjectiveDTOs[$arr['id']] = new SessionObjectiveDTO(
                $arr['id'],
                $arr['title'],
                $arr['position'],
                $arr['active'],
            );
        }
        $sessionObjectiveIds = array_keys($sessionObjectiveDTOs);

        $qb = $this->_em->createQueryBuilder()
            ->select(
                'x.id AS xId, session.id AS sessionId, ' .
                'course.id AS courseId, course.locked AS courseIsLocked, course.archived AS courseIsArchived, ' .
                'school.id AS schoolId'
            )
            ->from(SessionObjective::class, 'x')
            ->join('x.session', 'session')
            ->join('session.course', 'course')
            ->join('course.school', 'school')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $sessionObjectiveIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $sessionObjectiveDTOs[$arr['xId']]->session = (int) $arr['sessionId'];
            $sessionObjectiveDTOs[$arr['xId']]->courseIsLocked = (bool) $arr['courseIsLocked'];
            $sessionObjectiveDTOs[$arr['xId']]->courseIsArchived = (bool) $arr['courseIsArchived'];
            $sessionObjectiveDTOs[$arr['xId']]->course = (int) $arr['courseId'];
            $sessionObjectiveDTOs[$arr['xId']]->school = (int) $arr['schoolId'];
        }

        $qb = $this->_em->createQueryBuilder()
            ->select('x.id as id, a.id as ancestorId')
            ->from(SessionObjective::class, 'x')
            ->leftJoin('x.ancestor', 'a')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $sessionObjectiveIds);
        foreach ($qb->getQuery()->getResult() as $arr) {
            $sessionObjectiveDTOs[$arr['id']]->ancestor = $arr['ancestorId'] ? (int)$arr['ancestorId'] : null;
        }

        $related = [
            'terms',
            'courseObjectives',
            'meshDescriptors',
            'descendants'
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
     * @param array $criteria
     * @param array $orderBy
     * @param int $limit
     * @param int $offset
     * @return QueryBuilder
     */
    protected function attachCriteriaToQueryBuilder(QueryBuilder $qb, $criteria, $orderBy, $limit, $offset)
    {
        if (array_key_exists('courses', $criteria)) {
            if (is_array($criteria['courses'])) {
                $ids = $criteria['courses'];
            } else {
                $ids = [$criteria['courses']];
            }
            $qb->join('x.session', 'sc');
            $qb->join('sc.course', 'sc_c');
            $qb->andWhere($qb->expr()->in('sc_c.id', ':courses'));
            $qb->setParameter(':courses', $ids);
        }

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

        if (array_key_exists('sessions', $criteria)) {
            $ids = is_array($criteria['sessions']) ? $criteria['sessions'] : [$criteria['sessions']];
            $qb->join('x.session', 's');
            $qb->andWhere($qb->expr()->in('s.id', ':sessions'));
            $qb->setParameter(':sessions', $ids);
        }

        if (array_key_exists('schools', $criteria)) {
            $ids = is_array($criteria['schools']) ? $criteria['schools'] : [$criteria['schools']];
            $qb->join('x.session', 'session');
            $qb->join('session.course', 'course');
            $qb->join('course.school', 'school');
            $qb->andWhere($qb->expr()->in('school.id', ':schools'));
            $qb->setParameter(':schools', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['courses']);
        unset($criteria['terms']);
        unset($criteria['sessions']);
        unset($criteria['schools']);

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
        return (int) $this->_em->createQuery('SELECT COUNT(o.id) FROM App\Entity\SessionObjective o')
            ->getSingleScalarResult();
    }
}
