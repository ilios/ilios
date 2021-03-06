<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\ProgramYear;
use App\Traits\ManagerRepository;
use Doctrine\ORM\AbstractQuery;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use App\Entity\DTO\ProgramYearDTO;
use Doctrine\Persistence\ManagerRegistry;

class ProgramYearRepository extends ServiceEntityRepository implements
    DTORepositoryInterface,
    RepositoryInterface
{
    use ManagerRepository;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProgramYear::class);
    }

    /**
     * Custom findBy so we can filter by related entities
     *
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     * @return array
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT p')->from(ProgramYear::class, 'p');

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
        $qb = $this->_em->createQueryBuilder()->select('p')->distinct()->from(ProgramYear::class, 'p');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

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

    protected function attachAssociationsToDTOs(array $programYearDTOs): array
    {
        $programYearIds = array_keys($programYearDTOs);

        $qb = $this->_em->createQueryBuilder();
        $qb->select('py.id as programYearId, p.id as programId, c.id as cohortId, s.id as schoolId')
            ->from(ProgramYear::class, 'py')
            ->join('py.program', 'p')
            ->join('py.cohort', 'c')
            ->join('p.school', 's')

            ->where($qb->expr()->in('py.id', ':ids'))
            ->setParameter('ids', $programYearIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $programYearDTOs[$arr['programYearId']]->program = (int) $arr['programId'];
            $programYearDTOs[$arr['programYearId']]->cohort = (int) $arr['cohortId'];
            $programYearDTOs[$arr['programYearId']]->school = (int) $arr['schoolId'];
        }

        $related = [
            'directors',
            'competencies',
            'terms',
            'programYearObjectives',
        ];

        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id AS relId, p.id AS programYearId')->from(ProgramYear::class, 'p')
                ->join("p.{$rel}", 'r')
                ->where($qb->expr()->in('p.id', ':programYearIds'))
                ->orderBy('relId')
                ->setParameter('programYearIds', $programYearIds);

            foreach ($qb->getQuery()->getResult() as $arr) {
                $programYearDTOs[$arr['programYearId']]->{$rel}[] = $arr['relId'];
            }
        }

        return array_values($programYearDTOs);
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
            $ids = is_array($criteria['courses']) ? $criteria['courses'] : [$criteria['courses']];
            $qb->join('p.cohort', 'c_cohort');
            $qb->join('c_cohort.courses', 'c_course');
            $qb->andWhere($qb->expr()->in('c_course.id', ':courses'));
            $qb->setParameter(':courses', $ids);
        }

        if (array_key_exists('sessions', $criteria)) {
            $ids = is_array($criteria['sessions']) ? $criteria['sessions'] : [$criteria['sessions']];
            $qb->join('p.cohort', 'se_cohort');
            $qb->join('se_cohort.courses', 'se_course');
            $qb->join('se_course.sessions', 'se_session');
            $qb->andWhere($qb->expr()->in('se_session.id', ':sessions'));
            $qb->setParameter(':sessions', $ids);
        }

        if (array_key_exists('terms', $criteria)) {
            $ids = is_array($criteria['terms']) ? $criteria['terms'] : [$criteria['terms']];
            $qb->join('p.terms', 't_term');
            $qb->andWhere($qb->expr()->in('t_term.id', ':terms'));
            $qb->setParameter(':terms', $ids);
        }

        if (array_key_exists('schools', $criteria)) {
            $ids = is_array($criteria['schools']) ? $criteria['schools'] : [$criteria['schools']];
            $qb->join('p.program', 'py_program');
            $qb->join('py_program.school', 'py_school');
            $qb->andWhere($qb->expr()->in('py_school.id', ':schools'));
            $qb->setParameter(':schools', $ids);
        }

        if (array_key_exists('startYears', $criteria)) {
            $startYears = is_array($criteria['startYears']) ? $criteria['startYears'] : [$criteria['startYears']];
            $qb->andWhere($qb->expr()->in('p.startYear', ':startYears'));
            $qb->setParameter(':startYears', $startYears);
        }

        unset($criteria['schools']);
        unset($criteria['courses']);
        unset($criteria['sessions']);
        unset($criteria['terms']);
        unset($criteria['startYears']);

        if ($criteria !== []) {
            foreach ($criteria as $key => $value) {
                $values = is_array($value) ? $value : [$value];
                $qb->andWhere($qb->expr()->in("p.{$key}", ":{$key}"));
                $qb->setParameter(":{$key}", $values);
            }
        }

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('p.' . $sort, $order);
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
     * @param int $programYearId
     */
    public function getProgramYearObjectiveToCourseObjectivesMapping($programYearId): array
    {
        $qb = $this->_em->createQueryBuilder();
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
