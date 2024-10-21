<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ProgramYear;
use App\Service\DTOCacheManager;
use App\Traits\ManagerRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use App\Entity\DTO\ProgramYearDTO;
use Doctrine\Persistence\ManagerRegistry;

use function array_values;
use function array_keys;

class ProgramYearRepository extends ServiceEntityRepository implements
    DTORepositoryInterface,
    RepositoryInterface
{
    use ManagerRepository;

    public function __construct(
        ManagerRegistry $registry,
        protected DTOCacheManager $cacheManager,
    ) {
        parent::__construct($registry, ProgramYear::class);
    }

    public function hydrateDTOsFromIds(array $ids): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder()->select('x')->distinct()->from(ProgramYear::class, 'x');
        $qb->where($qb->expr()->in('x.id', ':ids'));
        $qb->setParameter(':ids', $ids);

        $programYearDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $programYearDTOs[$arr['id']] = new ProgramYearDTO(
                $arr['id'],
                $arr['startYear'],
                $arr['locked'],
                $arr['archived']
            );
        }

        return $this->attachAssociationsToDTOs($programYearDTOs);
    }

    protected function attachAssociationsToDTOs(array $dtos): array
    {
        if ($dtos === []) {
            return $dtos;
        }
        $programYearIds = array_keys($dtos);

        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select('py.id as programYearId, p.id as programId, c.id as cohortId, s.id as schoolId')
            ->from(ProgramYear::class, 'py')
            ->join('py.program', 'p')
            ->join('py.cohort', 'c')
            ->join('p.school', 's')

            ->where($qb->expr()->in('py.id', ':ids'))
            ->setParameter('ids', $programYearIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $dtos[$arr['programYearId']]->program = (int) $arr['programId'];
            $dtos[$arr['programYearId']]->cohort = (int) $arr['cohortId'];
            $dtos[$arr['programYearId']]->school = (int) $arr['schoolId'];
        }


        $dtos = $this->attachRelatedToDtos(
            $dtos,
            [
                'directors',
                'competencies',
                'terms',
                'programYearObjectives',
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
            $qb->join('x.cohort', 'c_cohort');
            $qb->join('c_cohort.courses', 'c_course');
            $qb->andWhere($qb->expr()->in('c_course.id', ':courses'));
            $qb->setParameter(':courses', $ids);
        }

        if (array_key_exists('sessions', $criteria)) {
            $ids = is_array($criteria['sessions']) ? $criteria['sessions'] : [$criteria['sessions']];
            $qb->join('x.cohort', 'se_cohort');
            $qb->join('se_cohort.courses', 'se_course');
            $qb->join('se_course.sessions', 'se_session');
            $qb->andWhere($qb->expr()->in('se_session.id', ':sessions'));
            $qb->setParameter(':sessions', $ids);
        }

        if (array_key_exists('terms', $criteria)) {
            $ids = is_array($criteria['terms']) ? $criteria['terms'] : [$criteria['terms']];
            $qb->join('x.terms', 't_term');
            $qb->andWhere($qb->expr()->in('t_term.id', ':terms'));
            $qb->setParameter(':terms', $ids);
        }

        if (array_key_exists('schools', $criteria)) {
            $ids = is_array($criteria['schools']) ? $criteria['schools'] : [$criteria['schools']];
            $qb->join('x.program', 'py_program');
            $qb->join('py_program.school', 'py_school');
            $qb->andWhere($qb->expr()->in('py_school.id', ':schools'));
            $qb->setParameter(':schools', $ids);
        }

        if (array_key_exists('startYears', $criteria)) {
            $startYears = is_array($criteria['startYears']) ? $criteria['startYears'] : [$criteria['startYears']];
            $qb->andWhere($qb->expr()->in('x.startYear', ':startYears'));
            $qb->setParameter(':startYears', $startYears);
        }

        unset($criteria['schools']);
        unset($criteria['courses']);
        unset($criteria['sessions']);
        unset($criteria['terms']);
        unset($criteria['startYears']);

        $this->attachClosingCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
    }

    public function getProgramYearObjectiveToCourseObjectivesMapping(int $programYearId): array
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select(
            "p.title AS program_title, py.startYear AS matriculation_year, pyo.title AS program_year_objective," .
                "cmp.title AS competency, c.title AS course_title, c.externalId AS course_shortname," .
                "co.title AS mapped_course_objective"
        )
            ->from(ProgramYear::class, 'py')
            ->join('py.program', 'p')
            ->join('py.programYearObjectives', 'pyo')
            ->leftJoin('pyo.competency', 'cmp')
            ->leftJoin('pyo.courseObjectives', 'co')
            ->leftJoin('co.course', 'c')
            ->where($qb->expr()->eq('py.id', ':id'))
            ->orderBy('pyo.id', 'ASC')
            ->addOrderBy('cmp.id', 'ASC')
            ->addOrderBy('c.id', 'ASC')
            ->addOrderBy('co.id', 'ASC')
            ->setParameter(':id', $programYearId);

        return $qb->getQuery()->getArrayResult();
    }
}
