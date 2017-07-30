<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\AbstractQuery;
use Ilios\CoreBundle\Entity\DTO\OfferingDTO;

/**
 * Class OfferingRepository
 */
class OfferingRepository extends EntityRepository implements DTORepositoryInterface
{
    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT x')->from('IliosCoreBundle:Offering', 'x');

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
        $qb = $this->_em->createQueryBuilder()->select('x')->distinct()->from('IliosCoreBundle:Offering', 'x');
        $this->attachCriteriaToQueryBuilder($qb, $criteria, $orderBy, $limit, $offset);
        $offeringDTOs = [];
        foreach ($qb->getQuery()->getResult(AbstractQuery::HYDRATE_ARRAY) as $arr) {
            $offeringDTOs[$arr['id']] = new OfferingDTO(
                $arr['id'],
                $arr['room'],
                $arr['site'],
                $arr['startDate'],
                $arr['endDate'],
                $arr['updatedAt']
            );
        }
        $offeringIds = array_keys($offeringDTOs);

        $qb = $this->_em->createQueryBuilder()
            ->select('x.id as offeringId, school.id as schoolId, course.id as courseId, session.id as sessionId')
            ->from('IliosCoreBundle:Offering', 'x')
            ->join('x.session', 'session')
            ->join('session.course', 'course')
            ->join('course.school', 'school')
            ->where($qb->expr()->in('x.id', ':offeringIds'))
            ->setParameter('offeringIds', $offeringIds);

        foreach ($qb->getQuery()->getResult() as $arr) {
            $offeringDTOs[$arr['offeringId']]->school = (int) $arr['schoolId'];
            $offeringDTOs[$arr['offeringId']]->course = (int) $arr['courseId'];
            $offeringDTOs[$arr['offeringId']]->session = (int) $arr['sessionId'];
        }

        $related = [
            'learnerGroups',
            'instructorGroups',
            'learners',
            'instructors'
        ];
        foreach ($related as $rel) {
            $qb = $this->_em->createQueryBuilder()
                ->select('r.id AS relId, x.id AS offeringId')->from('IliosCoreBundle:Offering', 'x')
                ->join("x.{$rel}", 'r')
                ->where($qb->expr()->in('x.id', ':offeringIds'))
                ->orderBy('relId')
                ->setParameter('offeringIds', $offeringIds);
            foreach ($qb->getQuery()->getResult() as $arr) {
                $offeringDTOs[$arr['offeringId']]->{$rel}[] = $arr['relId'];
            }
        }
        return array_values($offeringDTOs);
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
        $related = [
            'learnerGroups',
            'instructorGroups',
            'learners',
            'instructors'
        ];
        foreach ($related as $rel) {
            if (array_key_exists($rel, $criteria)) {
                $ids = is_array($criteria[$rel]) ?
                    $criteria[$rel] : [$criteria[$rel]];
                $alias = "alias_${rel}";
                $param = ":${rel}";
                $qb->join("x.${rel}", $alias);
                $qb->andWhere($qb->expr()->in("${alias}.id", $param));
                $qb->setParameter($param, $ids);
            }
            unset($criteria[$rel]);
        }

        if (array_key_exists('sessions', $criteria) || array_key_exists('courses', $criteria)) {
            $qb->join('x.session', 'x_session');
        }

        if (array_key_exists('sessions', $criteria)) {
            $ids = is_array($criteria['sessions']) ? $criteria['sessions'] : [$criteria['sessions']];
            $qb->andWhere($qb->expr()->in('x_session.id', ':sessions'));
            $qb->setParameter(':sessions', $ids);
            unset($criteria['sessions']);
        }

        if (array_key_exists('courses', $criteria)) {
            $ids = is_array($criteria['courses']) ? $criteria['courses'] : [$criteria['courses']];
            $qb->leftJoin('x_session.course', 'x_course');
            $qb->andWhere($qb->expr()->in('x_course.id', ':courses'));
            $qb->setParameter(':courses', $ids);
            unset($criteria['courses']);
        }

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
