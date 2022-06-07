<?php

declare(strict_types=1);

namespace App\Repository;

use App\Service\DTOCacheTagger;
use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\Cohort;
use App\Entity\DTO\CohortDTO;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Cache\CacheInterface;

use function array_values;
use function array_keys;

class CohortRepository extends ServiceEntityRepository implements DTORepositoryInterface, RepositoryInterface
{
    use ManagerRepository;

    public function __construct(
        ManagerRegistry $registry,
        protected CacheInterface $cache,
        protected DTOCacheTagger $cacheTagger,
    ) {
        parent::__construct($registry, Cohort::class);
    }

    public function hydrateDTOsFromIds(array $ids): array
    {
        $qb = $this->_em->createQueryBuilder()->select('x')->distinct()->from(Cohort::class, 'x');
        $qb->where($qb->expr()->in('x.id', ':ids'));
        $qb->setParameter(':ids', $ids);

        $dtos = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $dtos[$arr['id']] = new CohortDTO(
                $arr['id'],
                $arr['title']
            );
        }
        $cohortIds = array_keys($dtos);

        $qb = $this->_em->createQueryBuilder()
            ->select('c.id as cohortId, py.id as programYearId, p.id as programId, s.id as schoolId')
            ->from(Cohort::class, 'c')
            ->join('c.programYear', 'py')
            ->join('py.program', 'p')
            ->join('p.school', 's')
            ->where($qb->expr()->in('c.id', ':ids'))
            ->setParameter('ids', $cohortIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $dtos[$arr['cohortId']]->programYear = (int) $arr['programYearId'];
            $dtos[$arr['cohortId']]->program = (int) $arr['programId'];
            $dtos[$arr['cohortId']]->school = (int) $arr['schoolId'];
        }

        $dtos = $this->attachRelatedToDtos(
            $dtos,
            [
                'courses',
                'learnerGroups',
                'users'
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
            $ids = is_array($criteria['courses']) ? $criteria['courses'] : [$criteria['courses']];
            $qb->join('x.courses', 'c_courses');
            $qb->andWhere($qb->expr()->in('c_courses.id', ':courses'));
            $qb->setParameter(':courses', $ids);
        }

        if (array_key_exists('learnerGroups', $criteria)) {
            $ids = is_array($criteria['learnerGroups']) ? $criteria['learnerGroups'] : [$criteria['learnerGroups']];
            $qb->join('x.learnerGroups', 'c_learnerGroup');
            $qb->andWhere($qb->expr()->in('c_learnerGroup.id', ':learnerGroups'));
            $qb->setParameter(':learnerGroups', $ids);
        }

        if (array_key_exists('users', $criteria)) {
            $ids = is_array($criteria['users']) ? $criteria['users'] : [$criteria['users']];
            $qb->join('x.users', 'c_users');
            $qb->andWhere($qb->expr()->in('c_users.id', ':users'));
            $qb->setParameter(':users', $ids);
        }

        if (array_key_exists('schools', $criteria)) {
            $ids = is_array($criteria['schools']) ? $criteria['schools'] : [$criteria['schools']];
            $qb->join('x.programYear', 'c_school_programYear');
            $qb->join('c_school_programYear.program', 'c_school_program');
            $qb->join('c_school_program.school', 'c_school');
            $qb->andWhere($qb->expr()->in('c_school.id', ':schools'));
            $qb->setParameter(':schools', $ids);
        }

        if (array_key_exists('startYears', $criteria)) {
            $ids = is_array($criteria['startYears']) ? $criteria['startYears'] : [$criteria['startYears']];
            $qb->join('x.programYear', 'c_startYears_programYear');
            $qb->andWhere($qb->expr()->in('c_startYears_programYear.startYear', ':startYears'));
            $qb->setParameter(':startYears', $ids);
        }

        unset($criteria['courses']);
        unset($criteria['learnerGroups']);
        unset($criteria['users']);
        unset($criteria['schools']);
        unset($criteria['startYears']);

        $this->attachClosingCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
    }
}
