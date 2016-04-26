<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Ilios\CoreBundle\Entity\DTO\LearnerGroupDTO;

class LearnerGroupRepository extends EntityRepository
{
    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT l')->from('IliosCoreBundle:LearnerGroup', 'l');

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
        $qb = $this->_em->createQueryBuilder()->select('l')->distinct()->from('IliosCoreBundle:LearnerGroup', 'l');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);

        $learnerGroupDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $learnerGroupDTOs[$arr['id']] = new LearnerGroupDTO(
                $arr['id'],
                $arr['title'],
                $arr['location']
            );
        }
        $learnerGroupIds = array_keys($learnerGroupDTOs);

        $qb = $this->_em->createQueryBuilder()
            ->select('l.id as learnerGroupId, plg.id as parentId, c.id as cohortId')
            ->from('IliosCoreBundle:LearnerGroup', 'l')
            ->join('l.cohort', 'c')
            ->leftJoin('l.parent', 'plg')
            ->where($qb->expr()->in('l.id', ':ids'))
            ->setParameter('ids', $learnerGroupIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $learnerGroupDTOs[$arr['learnerGroupId']]->cohort = (int) $arr['cohortId'];
            $learnerGroupDTOs[$arr['learnerGroupId']]->parent = $arr['parentId']?(int)$arr['parentId']:null;
        }

        $related = [
            'children',
            'ilmSessions',
            'offerings',
            'instructorGroups',
            'users',
            'instructors'
        ];

        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id AS relId, l.id AS learnerGroupId')->from('IliosCoreBundle:LearnerGroup', 'l')
                ->join("l.{$rel}", 'r')
                ->where($qb->expr()->in('l.id', ':learnerGroupIds'))
                ->orderBy('relId')
                ->setParameter('learnerGroupIds', $learnerGroupIds);

            foreach ($qb->getQuery()->getResult() as $arr) {
                $learnerGroupDTOs[$arr['learnerGroupId']]->{$rel}[] = $arr['relId'];
            }
        }

        return array_values($learnerGroupDTOs);
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
                $qb->andWhere($qb->expr()->in("l.{$key}", ":{$key}"));
                $qb->setParameter(":{$key}", $values);
            }
        }

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }
        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('l.'.$sort, $order);
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
