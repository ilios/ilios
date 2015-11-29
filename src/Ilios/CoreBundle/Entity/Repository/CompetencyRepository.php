<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class CompetencyRepository
 * @package Ilios\CoreBundle\Entity\Repository
 */
class CompetencyRepository extends EntityRepository
{
    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('DISTINCT c')->from('IliosCoreBundle:Competency', 'c');

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('c.' . $sort, $order);
            }
        }

        if (array_key_exists('sessions', $criteria)) {
            $ids = is_array($criteria['sessions']) ? $criteria['sessions'] : [$criteria['sessions']];
            $qb->leftJoin('c.objectives', 'objective');
            $qb->leftJoin('objective.sessions', 'session');
            $qb->leftJoin('objective.children', 'objective2');
            $qb->leftJoin('objective2.sessions', 'session2');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('session.id', ':sessions'),
                    $qb->expr()->in('session2.id', ':sessions')
                )
            );
            $qb->setParameter(':sessions', $ids);
        }

        if (array_key_exists('sessionTypes', $criteria)) {
            $ids = is_array($criteria['sessionTypes']) ? $criteria['sessionTypes'] : [$criteria['sessionTypes']];
            $qb->leftJoin('c.objectives', 'objective');
            $qb->leftJoin('objective.sessions', 'session');
            $qb->leftJoin('session.sessionType', 'sessionType');
            $qb->leftJoin('objective.children', 'objective2');
            $qb->leftJoin('objective2.sessions', 'session2');
            $qb->leftJoin('session2.sessionType', 'sessionType2');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('sessionType.id', ':sessionTypes'),
                    $qb->expr()->in('sessionType2.id', ':sessionTypes')
                )
            );
            $qb->setParameter(':sessionTypes', $ids);
        }

        if (array_key_exists('courses', $criteria)) {
            $ids = is_array($criteria['courses']) ? $criteria['courses'] : [$criteria['courses']];
            $qb->leftJoin('c.objectives', 'objective');
            $qb->leftJoin('objective.courses', 'course');
            $qb->andWhere($qb->expr()->in('course.id', ':courses'));
            $qb->setParameter(':courses', $ids);
        }

        if (array_key_exists('topics', $criteria)) {
            $ids = is_array($criteria['topics']) ? $criteria['topics'] : [$criteria['topics']];
            $qb->leftJoin('c.objectives', 'objective');
            $qb->leftJoin('objective.courses', 'course');
            $qb->leftJoin('course.topics', 'topic');
            $qb->leftJoin('objective.sessions', 'session');
            $qb->leftJoin('session.topics', 'topic2');
            $qb->leftJoin('objective.children', 'objective2');
            $qb->leftJoin('objective2.sessions', 'session2');
            $qb->leftJoin('session2.topics', 'topic3');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('topic.id', ':topics'),
                    $qb->expr()->in('topic2.id', ':topics'),
                    $qb->expr()->in('topic3.id', ':topics')
                )
            );
            $qb->setParameter(':topics', $ids);
        }

        //cleanup all the possible relationship filters
        unset($criteria['courses']);
        unset($criteria['sessions']);
        unset($criteria['sessionTypes']);
        unset($criteria['topics']);

        if (count($criteria)) {
            foreach ($criteria as $key => $value) {
                $values = is_array($value) ? $value : [$value];
                $qb->andWhere($qb->expr()->in("c.{$key}", ":{$key}"));
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
