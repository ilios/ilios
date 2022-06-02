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

use function array_values;
use function array_keys;

class CourseObjectiveRepository extends ServiceEntityRepository implements DTORepositoryInterface, RepositoryInterface
{
    use ManagerRepository;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CourseObjective::class);
    }

    public function hydrateDTOsFromIds(array $ids): array
    {
        $qb = $this->_em->createQueryBuilder()->select('x')
            ->distinct()->from(CourseObjective::class, 'x');
        $qb->where($qb->expr()->in('x.id', ':ids'));
        $qb->setParameter(':ids', $ids);
        $dtos = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['id']] = new CourseObjectiveDTO(
                $arr['id'],
                $arr['title'],
                $arr['position'],
                $arr['active']
            );
        }
        $courseObjectiveIds = array_keys($dtos);

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
            $dtos[$arr['xId']]->courseIsLocked = (bool) $arr['courseIsLocked'];
            $dtos[$arr['xId']]->courseIsArchived = (bool) $arr['courseIsArchived'];
            $dtos[$arr['xId']]->course = (int) $arr['courseId'];
            $dtos[$arr['xId']]->school = (int) $arr['schoolId'];
        }

        $qb = $this->_em->createQueryBuilder()
            ->select('x.id as id, a.id as ancestorId')
            ->from(CourseObjective::class, 'x')
            ->leftJoin('x.ancestor', 'a')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $courseObjectiveIds);
        foreach ($qb->getQuery()->getResult() as $arr) {
            $dtos[$arr['id']]->ancestor = $arr['ancestorId'] ? (int)$arr['ancestorId'] : null;
        }

        $dtos = $this->attachRelatedToDtos(
            $dtos,
            [
                'terms',
                'programYearObjectives',
                'sessionObjectives',
                'meshDescriptors',
                'descendants'
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

        $this->attachClosingCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
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
