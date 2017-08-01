<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Ilios\CoreBundle\Entity\DTO\CurriculumInventoryAcademicLevelDTO;

/**
 * Class CurriculumInventoryAcademicLevelRepository
 */
class CurriculumInventoryAcademicLevelRepository extends EntityRepository implements DTORepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from('IliosCoreBundle:CurriculumInventoryAcademicLevel', 'x');

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
            ->distinct()->from('IliosCoreBundle:CurriculumInventoryAcademicLevel', 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        /** @var CurriculumInventoryAcademicLevelDTO[] $academicLevelDTOs */
        $academicLevelDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $academicLevelDTOs[$arr['id']] = new CurriculumInventoryAcademicLevelDTO(
                $arr['id'],
                $arr['name'],
                $arr['description'],
                $arr['level']
            );
        }
        $curriculumInventoryAcademicLevelIds = array_keys($academicLevelDTOs);

        $qb = $this->_em->createQueryBuilder()
            ->select(
                'x.id as xId, report.id AS reportId, school.id AS schoolId'
            )
            ->from('IliosCoreBundle:CurriculumInventoryAcademicLevel', 'x')
            ->join('x.report', 'report')
            ->join('report.program', 'program')
            ->join('program.school', 'school')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $curriculumInventoryAcademicLevelIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $academicLevelDTOs[$arr['xId']]->report = (int) $arr['reportId'];
            $academicLevelDTOs[$arr['xId']]->school = $arr['schoolId']?(int)$arr['schoolId']:null;
        }

        $related = [
            'sequenceBlocks'
        ];
        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id AS relId, x.id AS curriculumInventoryAcademicLevelId')
                ->from('IliosCoreBundle:CurriculumInventoryAcademicLevel', 'x')
                ->join("x.{$rel}", 'r')
                ->where($qb->expr()->in('x.id', ':ids'))
                ->orderBy('relId')
                ->setParameter('ids', $curriculumInventoryAcademicLevelIds);
            foreach ($qb->getQuery()->getResult() as $arr) {
                $academicLevelDTOs[$arr['curriculumInventoryAcademicLevelId']]->{$rel}[] = $arr['relId'];
            }
        }
        return array_values($academicLevelDTOs);
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
        if (array_key_exists('sequenceBlocks', $criteria)) {
            $ids = is_array($criteria['sequenceBlocks']) ? $criteria['sequenceBlocks'] : [$criteria['sequenceBlocks']];
            $qb->join('x.sequenceBlocks', 'sb');
            $qb->andWhere($qb->expr()->in('sb.id', ':sequenceBlocks'));
            $qb->setParameter(':sequenceBlocks', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['sequenceBlocks']);

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
