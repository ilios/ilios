<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Ilios\CoreBundle\Entity\DTO\LearnerGroupDTO;

class LearnerGroupRepository extends EntityRepository implements DTORepositoryInterface
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
        if (array_key_exists('cohort', $criteria)) {
            $criteria['cohorts'][] = $criteria['cohort'];
            unset($criteria['cohort']);
        }
        if (array_key_exists('cohorts', $criteria)) {
            $ids = is_array($criteria['cohorts']) ? $criteria['cohorts'] : [$criteria['cohorts']];
            $qb->join('l.cohort', 'l_cohort');
            $qb->andWhere($qb->expr()->in('l_cohort.id', ':cohorts'));
            $qb->setParameter(':cohorts', $ids);
        }

        if (array_key_exists('parent', $criteria)) {
            $criteria['parents'][] = $criteria['parent'];
            unset($criteria['parent']);
        }
        if (array_key_exists('parents', $criteria)) {
            $ids = is_array($criteria['parents'])
                ? $criteria['parents'] : [$criteria['parents']];
            if (in_array(null, $ids)) {
                $ids = array_diff($ids, [null]);
                $qb->andWhere('l.parent IS NULL');
            }
            if (count($ids)) {
                $qb->join('l.parent', 'l_parent');
                $qb->andWhere($qb->expr()->in('l_parent.id', ':parents'));
                $qb->setParameter(':parents', $ids);
            }
        }

        if (array_key_exists('terms', $criteria)) {
            $ids = is_array($criteria['terms']) ? $criteria['terms'] : [$criteria['terms']];
            $qb->leftJoin('l.offerings', 't_offering');
            $qb->leftJoin('t_offering.session', 't_session1');
            $qb->leftJoin('t_session1.terms', 't_term1');
            $qb->leftJoin('t_session1.course', 't_course1');
            $qb->leftJoin('t_course1.terms', 't_term2');
            $qb->leftJoin('l.ilmSessions', 't_ilm');
            $qb->leftJoin('t_ilm.session', 't_session2');
            $qb->leftJoin('t_session2.terms', 't_term3');
            $qb->leftJoin('t_session2.course', 't_course2');
            $qb->leftJoin('t_course2.terms', 't_term4');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('t_term1.id', ':terms'),
                    $qb->expr()->in('t_term2.id', ':terms'),
                    $qb->expr()->in('t_term3.id', ':terms'),
                    $qb->expr()->in('t_term4.id', ':terms')
                )
            );
            $qb->setParameter(':terms', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['cohorts']);
        unset($criteria['parents']);
        unset($criteria['terms']);

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
