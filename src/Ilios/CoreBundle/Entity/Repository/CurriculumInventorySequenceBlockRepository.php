<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Ilios\CoreBundle\Entity\DTO\CurriculumInventorySequenceBlockDTO;

/**
 * Class CurriculumInventorySequenceBlockRepository
 */
class CurriculumInventorySequenceBlockRepository extends EntityRepository implements DTORepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from('IliosCoreBundle:CurriculumInventorySequenceBlock', 'x');

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
            ->distinct()->from('IliosCoreBundle:CurriculumInventorySequenceBlock', 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        /** @var CurriculumInventorySequenceBlockDTO[] $sequenceBlockDTOs */
        $sequenceBlockDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $sequenceBlockDTOs[$arr['id']] = new CurriculumInventorySequenceBlockDTO(
                $arr['id'],
                $arr['title'],
                $arr['description'],
                $arr['required'],
                $arr['childSequenceOrder'],
                $arr['orderInSequence'],
                $arr['minimum'],
                $arr['maximum'],
                $arr['track'],
                $arr['startDate'],
                $arr['endDate'],
                $arr['duration']
            );
        }
        $curriculumInventorySequenceBlockIds = array_keys($sequenceBlockDTOs);

        $qb = $this->_em->createQueryBuilder()
            ->select(
                'x.id as xId, report.id AS reportId, school.id AS schoolId, ' .
                'academicLevel.id AS academicLevelId, course.id AS courseId, parent.id AS parentId '
            )
            ->from('IliosCoreBundle:CurriculumInventorySequenceBlock', 'x')
            ->join('x.report', 'report')
            ->join('report.program', 'program')
            ->join('program.school', 'school')
            ->leftJoin('x.parent', 'parent')
            ->leftJoin('x.course', 'course')
            ->leftJoin('x.academicLevel', 'academicLevel')
            ->where($qb->expr()->in('x.id', ':ids'))
            ->setParameter('ids', $curriculumInventorySequenceBlockIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $sequenceBlockDTOs[$arr['xId']]->report = (int) $arr['reportId'];
            $sequenceBlockDTOs[$arr['xId']]->academicLevel = $arr['academicLevelId']?(int)$arr['academicLevelId']:null;
            $sequenceBlockDTOs[$arr['xId']]->course = $arr['courseId']?(int)$arr['courseId']:null;
            $sequenceBlockDTOs[$arr['xId']]->parent = $arr['parentId']?(int)$arr['parentId']:null;
            $sequenceBlockDTOs[$arr['xId']]->school = $arr['schoolId'];
        }

        $related = [
            'children',
            'sessions'
        ];
        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id AS relId, x.id AS xId')
                ->from('IliosCoreBundle:CurriculumInventorySequenceBlock', 'x')
                ->join("x.{$rel}", 'r')
                ->where($qb->expr()->in('x.id', ':ids'))
                ->orderBy('relId')
                ->setParameter('ids', $curriculumInventorySequenceBlockIds);
            foreach ($qb->getQuery()->getResult() as $arr) {
                $sequenceBlockDTOs[$arr['xId']]->{$rel}[] = $arr['relId'];
            }
        }

        return array_values($sequenceBlockDTOs);
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
        if (array_key_exists('children', $criteria)) {
            $ids = is_array($criteria['children']) ? $criteria['children'] : [$criteria['children']];
            $qb->join('x.children', 'sb');
            $qb->andWhere($qb->expr()->in('sb.id', ':children'));
            $qb->setParameter(':children', $ids);
        }
        if (array_key_exists('sessions', $criteria)) {
            $ids = is_array($criteria['sessions']) ? $criteria['sessions'] : [$criteria['sessions']];
            $qb->join('x.sessions', 'sb');
            $qb->andWhere($qb->expr()->in('sb.id', ':sessions'));
            $qb->setParameter(':sessions', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['children']);
        unset($criteria['sessions']);

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
