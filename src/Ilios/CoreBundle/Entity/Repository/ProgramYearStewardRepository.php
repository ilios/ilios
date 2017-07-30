<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Ilios\CoreBundle\Entity\DTO\ProgramYearStewardDTO;

/**
 * Class ProgramYearStewardRepository
 */
class ProgramYearStewardRepository extends EntityRepository implements DTORepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from('IliosCoreBundle:ProgramYearSteward', 'x');

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
            ->distinct()->from('IliosCoreBundle:ProgramYearSteward', 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        /** @var ProgramYearStewardDTO[] $programYearStewardDTOs */
        $programYearStewardDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $programYearStewardDTOs[$arr['id']] = new ProgramYearStewardDTO(
                $arr['id']
            );
        }
        $programYearStewardIds = array_keys($programYearStewardDTOs);

        $qb = $this->_em->createQueryBuilder()
            ->select(
                'x.id as xId, department.id AS departmentId, ' .
                'programYear.id AS programYearId, school.id AS schoolId, ' .
                'owningProgram.id AS owningProgramId, owningSchool.id AS owningSchoolId'
            )
            ->from('IliosCoreBundle:ProgramYearSteward', 'x')
            ->join('x.programYear', 'programYear')
            ->join('programYear.program', 'owningProgram')
            ->join('owningProgram.school', 'owningSchool')
            ->leftJoin('x.department', 'department')
            ->leftJoin('x.school', 'school')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $programYearStewardIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $programYearStewardDTOs[$arr['xId']]->programYear = (int)$arr['programYearId'];
            $programYearStewardDTOs[$arr['xId']]->owningSchool = (int)$arr['owningSchoolId'];
            $programYearStewardDTOs[$arr['xId']]->owningProgram = (int)$arr['owningProgramId'];
            $programYearStewardDTOs[$arr['xId']]->department = $arr['departmentId']?(int)$arr['departmentId']:null;
            $programYearStewardDTOs[$arr['xId']]->school = $arr['schoolId']?(int)$arr['schoolId']:null;
        }

        return array_values($programYearStewardDTOs);
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
                $qb->addOrderBy('x.'.$sort, $order);
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
