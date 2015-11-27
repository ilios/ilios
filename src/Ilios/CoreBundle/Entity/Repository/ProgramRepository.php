<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

class ProgramRepository extends EntityRepository
{
    /**
     * Custom findBy so we can filter by related entities
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param null $limit
     * @param null $offset
     *
     * @return array
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();


        $qb->select('DISTINCT p')->from('IliosCoreBundle:Program', 'p');

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('p.'.$sort, $order);
            }
        }

        if (array_key_exists('courses', $criteria)) {
            $ids = is_array($criteria['courses']) ? $criteria['courses'] : [$criteria['courses']];
            $qb->join('p.programYears', 'programYear');
            $qb->join('programYear.cohort', 'cohort');
            $qb->join('cohort.courses', 'course');
            $qb->andWhere($qb->expr()->in('course.id', ':courses'));
            $qb->setParameter(':courses', $ids);
        }

        if (array_key_exists('sessions', $criteria)) {
            $ids = is_array($criteria['sessions']) ? $criteria['sessions'] : [$criteria['sessions']];
            $qb->join('p.programYears', 'programYear');
            $qb->join('programYear.cohort', 'cohort');
            $qb->join('cohort.courses', 'course');
            $qb->join('course.sessions', 'session');
            $qb->andWhere($qb->expr()->in('session.id', ':sessions'));
            $qb->setParameter(':sessions', $ids);
        }

        if (array_key_exists('topics', $criteria)) {
            $ids = is_array($criteria['topics']) ? $criteria['topics'] : [$criteria['topics']];
            $qb->join('p.programYears', 'programYear');
            $qb->join('programYear.topics', 'topic');
            $qb->andWhere($qb->expr()->in('topic.id', ':topics'));
            $qb->setParameter(':topics', $ids);
        }

        unset($criteria['courses']);
        unset($criteria['sessions']);
        unset($criteria['topics']);

        if (count($criteria)) {
            foreach ($criteria as $key => $value) {
                $values = is_array($value) ? $value : [$value];
                $qb->andWhere($qb->expr()->in("p.{$key}", ":{$key}"));
                $qb->setParameter(":{$key}", $values);
            }
        }
        if ($offset) {
            $qb->setFirstResult($offset);
        }

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult();
    }
}
