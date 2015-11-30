<?php
namespace Ilios\CoreBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Class InstructorGroupRepository
 * @package Ilios\CoreBundle\Entity\Repository
 */
class InstructorGroupRepository extends EntityRepository
{
    /**
     * @inheritdoc
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
    {
        $qb = $this->_em->createQueryBuilder();


        $qb->select('DISTINCT i')->from('IliosCoreBundle:InstructorGroup', 'i');

        if (empty($orderBy)) {
            $orderBy = ['id' => 'ASC'];
        }

        if (is_array($orderBy)) {
            foreach ($orderBy as $sort => $order) {
                $qb->addOrderBy('i.'.$sort, $order);
            }
        }

        if (array_key_exists('courses', $criteria)) {
            $ids = is_array($criteria['courses']) ? $criteria['courses'] : [$criteria['courses']];
            $qb->leftJoin('i.ilmSessions', 'ilm');
            $qb->leftJoin('i.offerings', 'offering');
            $qb->leftJoin('ilm.session', 'session');
            $qb->leftJoin('offering.session', 'session2');
            $qb->leftJoin('session.course', 'course');
            $qb->leftJoin('session2.course', 'course2');
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
            $qb->leftJoin('i.ilmSessions', 'ilm');
            $qb->leftJoin('i.offerings', 'offering');
            $qb->leftJoin('ilm.session', 'session');
            $qb->leftJoin('offering.session', 'session2');
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
            $qb->leftJoin('i.ilmSessions', 'ilm');
            $qb->leftJoin('i.offerings', 'offering');
            $qb->leftJoin('ilm.session', 'session');
            $qb->leftJoin('offering.session', 'session2');
            $qb->leftJoin('session.sessionType', 'sessionType');
            $qb->leftJoin('session2.sessionType', 'sessionType2');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('sessionType.id', ':sessionTypes'),
                    $qb->expr()->in('sessionType2.id', ':sessionTypes')
                )
            );
            $qb->setParameter(':sessionTypes', $ids);
        }

        if (array_key_exists('instructors', $criteria)) {
            $ids = is_array($criteria['instructors']) ? $criteria['instructors'] : [$criteria['instructors']];
            $qb->join('i.users', 'instructor');
            $qb->andWhere($qb->expr()->in('instructor.id', ':instructors'));
            $qb->setParameter(':instructors', $ids);
        }

        if (array_key_exists('learningMaterials', $criteria)) {
            $ids = is_array($criteria['learningMaterials'])
                ? $criteria['learningMaterials'] : [$criteria['learningMaterials']];
            $qb->leftJoin('i.ilmSessions', 'ilm');
            $qb->leftJoin('i.offerings', 'offering');
            $qb->leftJoin('ilm.session', 'session');
            $qb->leftJoin('offering.session', 'session2');
            $qb->leftJoin('session.learningMaterials', 'slm');
            $qb->leftJoin('session2.learningMaterials', 'slm2');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('slm.id', ':learningMaterials'),
                    $qb->expr()->in('slm2.id', ':learningMaterials')
                )
            );
            $qb->setParameter(':learningMaterials', $ids);
        }

        if (array_key_exists('topics', $criteria)) {
            $ids = is_array($criteria['topics'])
                ? $criteria['topics'] : [$criteria['topics']];
            $qb->leftJoin('i.ilmSessions', 'ilm');
            $qb->leftJoin('i.offerings', 'offering');
            $qb->leftJoin('ilm.session', 'session');
            $qb->leftJoin('offering.session', 'session2');
            $qb->leftJoin('session.topics', 'topic');
            $qb->leftJoin('session2.topics', 'topic2');
            $qb->andWhere(
                $qb->expr()->orX(
                    $qb->expr()->in('topic.id', ':topics'),
                    $qb->expr()->in('topic2.id', ':topics')
                )
            );
            $qb->setParameter(':topics', $ids);
        }

        unset($criteria['courses']);
        unset($criteria['sessions']);
        unset($criteria['sessionTypes']);
        unset($criteria['instructors']);
        unset($criteria['learningMaterials']);
        unset($criteria['topics']);

        if (count($criteria)) {
            foreach ($criteria as $key => $value) {
                $values = is_array($value) ? $value : [$value];
                $qb->andWhere($qb->expr()->in("i.{$key}", ":{$key}"));
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
