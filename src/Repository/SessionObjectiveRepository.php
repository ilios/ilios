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

use function array_values;
use function array_keys;

class SessionObjectiveRepository extends ServiceEntityRepository implements DTORepositoryInterface, RepositoryInterface
{
    use ManagerRepository;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SessionObjective::class);
    }

    public function hydrateDTOsFromIds(array $ids): array
    {
        $qb = $this->_em->createQueryBuilder()->select('x')
            ->distinct()->from(SessionObjective::class, 'x');
        $qb->where($qb->expr()->in('x.id', ':ids'));
        $qb->setParameter(':ids', $ids);
        $dtos = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['id']] = new SessionObjectiveDTO(
                $arr['id'],
                $arr['title'],
                $arr['position'],
                $arr['active'],
            );
        }
        $sessionObjectiveIds = array_keys($dtos);

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
            $dtos[$arr['xId']]->session = (int) $arr['sessionId'];
            $dtos[$arr['xId']]->courseIsLocked = (bool) $arr['courseIsLocked'];
            $dtos[$arr['xId']]->courseIsArchived = (bool) $arr['courseIsArchived'];
            $dtos[$arr['xId']]->course = (int) $arr['courseId'];
            $dtos[$arr['xId']]->school = (int) $arr['schoolId'];
        }

        $qb = $this->_em->createQueryBuilder()
            ->select('x.id as id, a.id as ancestorId')
            ->from(SessionObjective::class, 'x')
            ->leftJoin('x.ancestor', 'a')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $sessionObjectiveIds);
        foreach ($qb->getQuery()->getResult() as $arr) {
            $dtos[$arr['id']]->ancestor = $arr['ancestorId'] ? (int)$arr['ancestorId'] : null;
        }

        $dtos = $this->attachRelatedToDtos(
            $dtos,
            [
                'terms',
                'courseObjectives',
                'meshDescriptors',
                'descendants',
            ],
        );

        return array_values($dtos);
    }


    protected function attachCriteriaToQueryBuilder(
        QueryBuilder $qb,
        array $criteria,
        ?array $orderBy,
        ?int $limit,
        ?int $offset
    ): void {
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

        $this->attachClosingCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
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
