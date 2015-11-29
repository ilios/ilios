<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class TopicRepository
 * @package Ilios\CoreBundle\Entity\Repository
 */
class TopicRepository extends EntityRepository
{
    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();


        $qb->select('DISTINCT t')->from('IliosCoreBundle:Topic', 't');

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('t.'.$sort, $order);
            }
        }

        if (array_key_exists('courses', $criteria)) {
            $ids = is_array($criteria['courses']) ? $criteria['courses'] : [$criteria['courses']];
            $qb->leftJoin('t.courses', 'course');
            $qb->leftJoin('t.sessions', 'session');
            $qb->leftJoin('session.course', 'course2');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('course.id', ':courses'),
                    $qb->expr()->in('course2.id', ':courses')
                )
            );
            $qb->setParameter(':courses', $ids);
        }

        if (array_key_exists('sessions', $criteria)) {
            $ids = is_array($criteria['sessions']) ? $criteria['sessions'] : [$criteria['sessions']];
            $qb->join('t.sessions', 'session');
            $qb->andWhere($qb->expr()->in('session.id', ':sessions'));
            $qb->setParameter(':sessions', $ids);
        }

        if (array_key_exists('sessionTypes', $criteria)) {
            $ids = is_array($criteria['sessionTypes']) ? $criteria['sessionTypes'] : [$criteria['sessionTypes']];
            // @todo implement filter. [ST 2015/11/28]
        }

        if (array_key_exists('programs', $criteria)) {
            $ids = is_array($criteria['programs']) ? $criteria['programs'] : [$criteria['programs']];
            // @todo implement filter. [ST 2015/11/28]
        }

        if (array_key_exists('instructors', $criteria)) {
            $ids = is_array($criteria['instructors']) ? $criteria['instructors'] : [$criteria['instructors']];
            // @todo implement filter. [ST 2015/11/28]
        }

        if (array_key_exists('instructorGroups', $criteria)) {
            $ids = is_array($criteria['instructorGroups'])
                ? $criteria['instructorGroups'] : [$criteria['instructorGroups']];
            // @todo implement filter. [ST 2015/11/28]
        }

        if (array_key_exists('learningMaterials', $criteria)) {
            $ids = is_array($criteria['learningMaterials'])
                ? $criteria['learningMaterials'] : [$criteria['learningMaterials']];
            // @todo implement filter. [ST 2015/11/28]
        }

        if (array_key_exists('competencies', $criteria)) {
            $ids = is_array($criteria['competencies']) ? $criteria['competencies'] : [$criteria['competencies']];
            // @todo implement filter. [ST 2015/11/28]
        }

        if (array_key_exists('meshDescriptors', $criteria)) {
            $ids = is_array($criteria['meshDescriptors'])
                ? $criteria['meshDescriptors'] : [$criteria['meshDescriptors']];
            // @todo implement filter. [ST 2015/11/28]
        }

        unset($criteria['courses']);
        unset($criteria['sessions']);
        unset($criteria['sessionTypes']);
        unset($criteria['programs']);
        unset($criteria['instructors']);
        unset($criteria['instructorGroups']);
        unset($criteria['learningMaterials']);
        unset($criteria['competencies']);
        unset($criteria['meshDescriptors']);

        if (count($criteria)) {
            foreach ($criteria as $key => $value) {
                $values = is_array($value) ? $value : [$value];
                $qb->andWhere($qb->expr()->in("t.{$key}", ":{$key}"));
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
