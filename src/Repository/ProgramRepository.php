<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Program;
use App\Traits\ManagerRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use App\Entity\DTO\ProgramDTO;
use Doctrine\Persistence\ManagerRegistry;

class ProgramRepository extends ServiceEntityRepository implements
    DTORepositoryInterface,
    RepositoryInterface
{
    use ManagerRepository;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Program::class);
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
        $qb->select('DISTINCT p')->from(Program::class, 'p');

        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        return $qb->getQuery()->getResult();
    }

    /**
     * @inheritdoc
     */
    public function findDTOsBy(array $criteria, array $orderBy = null, $limit = null, $offset = null): array
    {
        $qb = $this->_em->createQueryBuilder()->select('p')->distinct()->from(Program::class, 'p');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
        $programDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $programDTOs[$arr['id']] = new ProgramDTO(
                $arr['id'],
                $arr['title'],
                $arr['shortTitle'],
                $arr['duration']
            );
        }
        return $this->attachAssociationsToDTOs($programDTOs);
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
            $qb->join('p.programYears', 'c_programYear');
            $qb->join('c_programYear.cohort', 'c_cohort');
            $qb->join('c_cohort.courses', 'c_course');
            $qb->andWhere($qb->expr()->in('c_course.id', ':courses'));
            $qb->setParameter(':courses', $ids);
        }

        if (array_key_exists('sessions', $criteria)) {
            $ids = is_array($criteria['sessions']) ? $criteria['sessions'] : [$criteria['sessions']];
            $qb->join('p.programYears', 'se_programYear');
            $qb->join('se_programYear.cohort', 'se_cohort');
            $qb->join('se_cohort.courses', 'se_course');
            $qb->join('se_course.sessions', 'se_session');
            $qb->andWhere($qb->expr()->in('se_session.id', ':sessions'));
            $qb->setParameter(':sessions', $ids);
        }

        if (array_key_exists('terms', $criteria)) {
            $ids = is_array($criteria['terms']) ? $criteria['terms'] : [$criteria['terms']];
            $qb->join('p.programYears', 't_programYear');
            $qb->join('t_programYear.terms', 't_term');
            $qb->andWhere($qb->expr()->in('t_term.id', ':terms'));
            $qb->setParameter(':terms', $ids);
        }

        if (array_key_exists('schools', $criteria)) {
            $ids = is_array($criteria['schools']) ? $criteria['schools'] : [$criteria['schools']];
            $qb->join('p.school', 'sc_school');
            $qb->andWhere($qb->expr()->in('sc_school.id', ':schools'));
            $qb->setParameter(':schools', $ids);
        }

        unset($criteria['schools']);
        unset($criteria['courses']);
        unset($criteria['sessions']);
        unset($criteria['terms']);

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

    protected function attachAssociationsToDTOs(array $programDTOs): array
    {
        $programIds = array_keys($programDTOs);
        $qb = $this->_em->createQueryBuilder();
        $qb->select('p.id as programId, s.id as schoolId')
            ->from(Program::class, 'p')
            ->join('p.school', 's')
            ->where($qb->expr()->in('p.id', ':ids'))
            ->setParameter('ids', $programIds);
        foreach ($qb->getQuery()->getResult() as $arr) {
            $programDTOs[$arr['programId']]->school = (int) $arr['schoolId'];
        }
        $related = [
            'programYears',
            'curriculumInventoryReports',
            'directors'
        ];
        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id AS relId, p.id AS programId')->from(Program::class, 'p')
                ->join("p.{$rel}", 'r')
                ->where($qb->expr()->in('p.id', ':programIds'))
                ->orderBy('relId')
                ->setParameter('programIds', $programIds);
            foreach ($qb->getQuery()->getResult() as $arr) {
                $programDTOs[$arr['programId']]->{$rel}[] = $arr['relId'];
            }
        }
        return array_values($programDTOs);
    }
}
